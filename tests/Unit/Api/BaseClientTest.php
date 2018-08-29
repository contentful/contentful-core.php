<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2018 Contentful GmbH
 * @license   MIT
 */

namespace Contentful\Tests\Core\Unit\Api;

use Contentful\Core\Api\BaseClient;
use Contentful\Core\Api\Exception;
use Contentful\Tests\Core\TestCase;
use Contentful\Tests\Core\Unit\Api\Exception\BadRequestException;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use Psr\Http\Message\RequestInterface;
use function GuzzleHttp\Psr7\parse_request as guzzle_parse_request;
use function GuzzleHttp\Psr7\parse_response as guzzle_parse_response;

class BaseClientTest extends TestCase
{
    /**
     * @var \Closure
     */
    private $requestHandler;

    public function createHttpClient()
    {
        $stack = new HandlerStack();
        $stack->setHandler(new CurlHandler());
        $stack->push(function (callable $handler) {
            return function (RequestInterface $request, array $options) use ($handler) {
                $handler = $this->requestHandler ?: $handler;

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
        $client = new ConcreteClient('b4c0n73n7fu1', 'https://cdn.contentful.com/', $logger, $httpClient);
        $client->setApplication('sdk-test-application', '1.0');

        $this->assertSame('DELIVERY', $client->getApi());
        $this->assertSame('https://cdn.contentful.com', $client->getHost());

        $jsonResponse = $client->request('GET', '/spaces/cfexampleapi');

        $this->assertSame('cfexampleapi', $jsonResponse['sys']['id']);
        $logs = $handler->getRecords();
        $this->assertCount(2, $logs);

        $this->assertSame('INFO', $logs[0]['level_name']);
        $this->assertRegExp('/GET https\:\/\/cdn\.contentful\.com\/spaces\/cfexampleapi \(([0-9]{1,})\.([0-9]{3})s\)/', $logs[0]['message']);

        $this->assertSame('DEBUG', $logs[1]['level_name']);
        $context = $logs[1]['context'];
        $this->assertSame('DELIVERY', $context['api']);
        $this->assertInternalType('float', $context['duration']);
        $this->assertNull(\unserialize($context['exception']));

        try {
            $request = guzzle_parse_request($context['request']);
            if ($context['response']) {
                $response = guzzle_parse_response($context['response']);
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
            '/^app sdk-test-application\/1.0; sdk contentful-core.php\/[0-9\.]*(-(dev|beta|alpha|RC))?; platform PHP\/[0-9\.]*; os (Windows|Linux|macOS);$/',
            $request->getHeaderLine('X-Contentful-User-Agent')
        );
        $this->assertFalse($request->hasHeader('Content-Type'));
        $this->assertSame('application/vnd.contentful.delivery.v1+json', $request->getHeaderLine('Accept'));
    }

    /**
     * @expectedException        \Contentful\Core\Exception\NotFoundException
     * @expectedExceptionMessage The resource could not be found.
     */
    public function testErrorPage()
    {
        $httpClient = $this->createHttpClient();
        $client = new ConcreteClient('b4c0n73n7fu1', 'https://cdn.contentful.com', \null, $httpClient);

        $this->requestHandler = function (RequestInterface $request, array $options) {
            $response = new Response(404, [], $this->getFixtureContent('not_found.json'));

            throw new ClientException('Not Found', $request, $response);
        };

        $client->request('GET', '/spaces/invalid');
    }

    public function testCustomException()
    {
        $httpClient = $this->createHttpClient();
        $client = new CustomExceptionConcreteClient('b4c0n73n7fu1', 'https://api.contentful.com', \null, $httpClient);
        $client->setIntegration('sdk-test-integration', '1.0.0-beta');

        $this->assertSame('MANAGEMENT', $client->getApi());
        $this->assertSame('https://api.contentful.com', $client->getHost());

        $this->requestHandler = function (RequestInterface $request, array $options) {
            $response = new Response(
                401,
                ['X-Contentful-Request-Id' => 'd533d76293f8bb047467344a28beffe0'],
                $this->getFixtureContent('bad_request.json')
            );

            throw new ClientException('Bad Request', $request, $response);
        };

        try {
            $client->request('POST', '/custom-url', [
                'query' => ['someVar' => 'someValue', 'anotherVar' => 'anotherValue'],
                'headers' => ['X-Contentful-Is' => 'Awesome'],
                'body' => '{"message": "Hello, world!"}',
                'baseUri' => 'https://www.example.com',
            ]);
        } catch (Exception $exception) {
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
                '/^integration sdk-test-integration\/1.0.0-beta; sdk contentful-core.php\/[0-9\.]*(-(dev|beta|alpha|RC))?; platform PHP\/[0-9\.]*; os (Windows|Linux|macOS);$/',
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
}

class ConcreteClient extends BaseClient
{
    public function request($method, $path, array $options = [])
    {
        return parent::request($method, $path, $options);
    }

    public function getApi()
    {
        return 'DELIVERY';
    }

    protected function getSdkName()
    {
        return 'contentful-core.php';
    }

    protected function getSdkVersion()
    {
        return '1.0';
    }

    protected function getApiContentType()
    {
        return 'application/vnd.contentful.delivery.v1+json';
    }
}

class CustomExceptionConcreteClient extends BaseClient
{
    public function request($method, $path, array $options = [])
    {
        return parent::request($method, $path, $options);
    }

    public function getApi()
    {
        return 'MANAGEMENT';
    }

    protected function getSdkName()
    {
        return 'contentful-core.php';
    }

    protected function getSdkVersion()
    {
        return '1.0';
    }

    protected function getApiContentType()
    {
        return 'application/vnd.contentful.management.v1+json';
    }

    protected function getExceptionNamespace()
    {
        return __NAMESPACE__.'\\Exception';
    }
}
