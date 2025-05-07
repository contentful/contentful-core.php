<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2025 Contentful GmbH
 * @license   MIT
 */

declare(strict_types=1);

namespace Contentful\Tests\Core\Unit\Api;

use Contentful\Core\Exception\NotFoundException;
use Contentful\Tests\Core\Implementation\Application;
use Contentful\Tests\Core\Implementation\Client;
use Contentful\Tests\Core\Implementation\ClientCustomException;
use Contentful\Tests\Core\Implementation\Exception\BadRequestException;
use Contentful\Tests\Core\Implementation\Integration;
use Contentful\Tests\Core\Implementation\InvalidPackageNameClient;
use Contentful\Tests\TestCase;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Message as GuzzleMessage;
use GuzzleHttp\Psr7\Response;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class BaseClientTest extends TestCase
{
    public function createHttpClient(?callable $handlerOverride = null)
    {
        $stack = new HandlerStack();
        $stack->setHandler(new CurlHandler());
        $stack->push(function (callable $handler) use ($handlerOverride) {
            return function (RequestInterface $request, array $options) use ($handler, $handlerOverride) {
                $handler = $handlerOverride ?: $handler;

                return $handler($request, $options);
            };
        });

        return new HttpClient(['handler' => $stack]);
    }

    public function testClient()
    {
        $handler = new TestHandler();
        $logger = new Logger('test', [$handler]);
        $httpClient = $this->createHttpClient();
        $client = new Client('b4c0n73n7fu1', 'https://cdn.contentful.com/', $logger, $httpClient);
        $client->setApplication('sdk-test-application', '1.0');

        $this->assertSame('DELIVERY', $client->getApi());
        $this->assertSame('https://cdn.contentful.com', $client->getHost());
        $this->assertSame($logger, $client->getLogger());

        $jsonResponse = $client->callApi('GET', '/spaces/cfexampleapi');

        $this->assertSame('cfexampleapi', $jsonResponse['sys']['id']);
        $logs = $handler->getRecords();
        $this->assertCount(2, $logs);

        $this->assertSame('INFO', $logs[0]['level_name']);
        $this->assertRegExp('/GET https\:\/\/cdn\.contentful\.com\/spaces\/cfexampleapi \(([0-9]{1,})\.([0-9]{3})s\)/', $logs[0]['message']);

        $this->assertSame('DEBUG', $logs[1]['level_name']);
        $context = $logs[1]['context'];
        $this->assertSame('DELIVERY', $context['api']);
        $this->assertIsFloat($context['duration']);
        $this->assertNull(unserialize($context['exception']));

        try {
            $request = GuzzleMessage::parseRequest($context['request']);
            if ($context['response']) {
                $response = GuzzleMessage::parseResponse($context['response']);
                $this->assertSame(200, $response->getStatusCode());
            }
        } catch (\Exception $exception) {
            $this->fail('Creating request and response from strings failed');

            return;
        }

        // String representations of HTTP messages have no real way of storing the HTTPS vs HTTP
        // information. Because of this, after serialization the protocol is defaulted to HTTP.
        // To get the original request, use a Message object retrieved from BaseClient::getMessages().
        $this->assertSame('http://cdn.contentful.com/spaces/cfexampleapi', (string) $request->getUri());
        $this->assertSame('Bearer b4c0n73n7fu1', $request->getHeaderLine('Authorization'));
        $this->assertRegExp(
            '/^app sdk-test-application\/1.0; sdk contentful-core.php\/(dev-master|[0-9\.]*(-(dev|beta|alpha|RC))?); platform PHP\/[0-9\.]*; os (Windows|Linux|macOS);$/',
            $request->getHeaderLine('X-Contentful-User-Agent')
        );
        $this->assertFalse($request->hasHeader('Content-Type'));
        $this->assertSame('application/vnd.contentful.delivery.v1+json', $request->getHeaderLine('Accept'));
    }

    public function testErrorPage()
    {
        $httpClient = $this->createHttpClient(function (RequestInterface $request, array $options) {
            $response = new Response(404, [], $this->getFixtureContent('not_found.json'));

            throw new ClientException('Not Found', $request, $response);
        });
        $client = new Client('b4c0n73n7fu1', 'https://cdn.contentful.com', null, $httpClient);

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('The resource could not be found.');

        $client->callApi('GET', '/spaces/invalid');
    }

    public function testCustomException()
    {
        $httpClient = $this->createHttpClient(function (RequestInterface $request) {
            $response = new Response(
                401,
                ['X-Contentful-Request-Id' => 'd533d76293f8bb047467344a28beffe0'],
                $this->getFixtureContent('bad_request.json')
            );

            throw new ClientException('Bad Request', $request, $response);
        });

        $client = new ClientCustomException('b4c0n73n7fu1', 'https://api.contentful.com', null, $httpClient);
        $client->setIntegration('sdk-test-integration', '1.0.0-beta');

        $this->assertSame('MANAGEMENT', $client->getApi());
        $this->assertSame('https://api.contentful.com', $client->getHost());

        try {
            $client->callApi('POST', '/custom-url', [
                'query' => ['someVar' => 'someValue', 'anotherVar' => 'anotherValue'],
                'headers' => ['X-Contentful-Is' => 'Awesome'],
                'body' => '{"message": "Hello, world!"}',
                'host' => 'https://www.example.com',
            ]);
        } catch (BadRequestException $exception) {
            $this->assertInstanceOf(BadRequestException::class, $exception);
            $this->assertSame('Unknown locale: invalidLocale', $exception->getMessage());
            $this->assertSame('What kind of request did you send?', $exception->getBadRequestMessage());
            $this->assertSame('d533d76293f8bb047467344a28beffe0', $exception->getRequestId());

            $exceptionRequest = $exception->getRequest();
            $this->assertSame('POST', $exceptionRequest->getMethod());
            $this->assertSame('{"message": "Hello, world!"}', (string) $exceptionRequest->getBody());
            $this->assertSame('https://www.example.com/custom-url?someVar=someValue&anotherVar=anotherValue', (string) $exceptionRequest->getUri());
            $this->assertSame('Awesome', $exceptionRequest->getHeaderLine('X-Contentful-Is'));
            $this->assertSame('application/vnd.contentful.management.v1+json', $exceptionRequest->getHeaderLine('Content-Type'));
            $this->assertRegExp(
                '/^integration sdk-test-integration\/1.0.0-beta; sdk contentful-core.php\/(dev-master|[0-9\.]*(-(dev|beta|alpha|RC))?); platform PHP\/[0-9\.]*; os (Windows|Linux|macOS);$/',
                $exceptionRequest->getHeaderLine('X-Contentful-User-Agent')
            );

            $messageRequest = $client->getMessages()[0]->getRequest();
            $this->assertSame('POST', $messageRequest->getMethod());
            $this->assertSame('{"message": "Hello, world!"}', (string) $messageRequest->getBody());
            $this->assertSame('https://www.example.com/custom-url?someVar=someValue&anotherVar=anotherValue', (string) $messageRequest->getUri());
            $this->assertSame('Awesome', $messageRequest->getHeaderLine('X-Contentful-Is'));
            $this->assertSame('application/vnd.contentful.management.v1+json', $messageRequest->getHeaderLine('Content-Type'));

            $this->assertSame(401, $exception->getResponse()->getStatusCode());
        }
    }

    public function testInvalidPackageNameVersion()
    {
        $httpClient = $this->createHttpClient(function (): ResponseInterface {
            return new Response(200);
        });

        $client = new InvalidPackageNameClient('b4c0n73n7fu1', 'https://cdn.contentful.com', null, $httpClient);
        $client->callApi('GET', '/');

        $request = $client->getMessages()[0]->getRequest();
        // When the current package name is invalid,
        // the version will automatically be set to 0.0.0-alpha
        $this->assertRegExp(
            '/sdk invalid\/0.0.0-alpha; platform PHP\/[0-9\.]*; os (Windows|Linux|macOS);$/',
            $request->getHeaderLine('X-Contentful-User-Agent')
        );
    }

    public function testCustomApplication()
    {
        $httpClient = $this->createHttpClient(function (): ResponseInterface {
            return new Response(201);
        });
        $client = new Client('irrelevant', 'https://cdn.contentful.com', null, $httpClient);

        $client->useApplication(new Application(false));
        $client->callApi('GET', '/');

        $request = $client->getMessages()[0]->getRequest();
        $this->assertRegExp(
            '/^app the-example-app\/1.0.0; sdk contentful-core.php\/(dev-master|[0-9\.]*(-(dev|beta|alpha|RC))?); platform PHP\/[0-9\.]*; os (Windows|Linux|macOS);$/',
            $request->getHeaderLine('X-Contentful-User-Agent')
        );

        $client->useApplication(new Application(true));
        $client->callApi('GET', '/');

        $request = $client->getMessages()[1]->getRequest();
        $this->assertRegExp(
            '/^app the-example-app\/0.0.0-alpha; sdk contentful-core.php\/(dev-master|[0-9\.]*(-(dev|beta|alpha|RC))?); platform PHP\/[0-9\.]*; os (Windows|Linux|macOS);$/',
            $request->getHeaderLine('X-Contentful-User-Agent')
        );
    }

    public function testCustomIntegration()
    {
        $httpClient = $this->createHttpClient(function (): ResponseInterface {
            return new Response(201);
        });
        $client = new Client('irrelevant', 'https://cdn.contentful.com', null, $httpClient);

        $client->useIntegration(new Integration());
        $client->callApi('GET', '/');

        $request = $client->getMessages()[0]->getRequest();
        $this->assertRegExp(
            '/^integration contentful.symfony\/0.0.0-alpha; sdk contentful-core.php\/(dev-master|[0-9\.]*(-(dev|beta|alpha|RC))?); platform PHP\/[0-9\.]*; os (Windows|Linux|macOS);$/',
            $request->getHeaderLine('X-Contentful-User-Agent')
        );
    }

    public function testClearMessages()
    {
        $logger = null;
        $httpClient = $this->createHttpClient();
        $client = new Client('b4c0n73n7fu1', 'https://cdn.contentful.com/', $logger, $httpClient);
        $client->setApplication('sdk-test-application', '1.0');

        $client->callApi('GET', '/spaces/cfexampleapi');
        $this->assertNotEmpty($client->getMessages());
        $client->clearMesssages();
        $this->assertEmpty($client->getMessages());
    }

    /**
     * @dataProvider storingMessagesProvider
     */
    public function testStoringMessages($storeMessages, $expectedCount)
    {
        $logger = null;
        $httpClient = $this->createHttpClient();
        $client = new Client('b4c0n73n7fu1', 'https://cdn.contentful.com/', $logger, $httpClient, $storeMessages);
        $client->setApplication('sdk-test-application', '1.0');

        $client->callApi('GET', '/spaces/cfexampleapi');
        $this->assertCount($expectedCount, $client->getMessages());
    }

    public function storingMessagesProvider()
    {
        return [
            'false' => [
                'storeMessages' => false,
                'expectedCount' => 0,
            ],
            'true' => [
                'storeMessages' => true,
                'expectedCount' => 1,
            ],
        ];
    }
}
