<?php

/**
 * This file is part of the contentful-core.php package.
 *
 * @copyright 2015-2018 Contentful GmbH
 * @license   MIT
 */

namespace Contentful\Tests\Core\Unit\Api;

use Contentful\Core\Api\Exception;
use Contentful\Core\Api\Message;
use Contentful\Tests\Core\TestCase;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class MessageTest extends TestCase
{
    public function testSetGetData()
    {
        $request = new Request(
            'POST',
            'http://www.example.com/',
            ['Authorization' => 'Bearer <accessToken>'],
            '{"message":"Hello!"}'
        );
        $response = new Response(
            404,
            [],
            '{"message":"Hello to who?"}'
        );
        $exception = new Exception(new RequestException('Not Found', $request, $response));

        $message = new Message(
            'DELIVERY',
            0.5,
            $request,
            $response,
            $exception
        );

        $this->assertSame('DELIVERY', $message->getApi());
        $this->assertSame(0.5, $message->getDuration());
        $this->assertSame($request, $message->getRequest());
        $this->assertSame($response, $message->getResponse());
        $this->assertInstanceOf(\Exception::class, $message->getException());

        $this->assertSame('ERROR', $message->getLogLevel());
        $this->assertTrue($message->isError());

        // Exceptions and their stack traces are a bit brittle to test,
        // so message.json might need to be updated from time to time.
        $this->assertJsonFixtureEqualsJsonObject('message.json', $message);
        $this->assertJsonFixtureEqualsJsonString('message.json', (string) $message);
        $this->assertJsonFixtureEqualsJsonString('message.json', $message->asString());
    }

    /**
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage Unknown API value "INVALID_API".
     */
    public function testInvalidApi()
    {
        $request = new Request(
            'POST',
            'http://www.example.com/',
            ['Authorization' => 'Bearer <accessToken>'],
            '{"message":"Hello!"}'
        );
        $response = new Response(
            404,
            [],
            '{"message":"Hello to who?"}'
        );
        $requestException = new RequestException('Not Found', $request, $response);
        $exception = new Exception($requestException);

        new Message('INVALID_API', 0.5, $request, $response, $exception);
    }

    public function testCreateFromString()
    {
        $message = Message::createFromString($this->getFixtureContent('message.json'));

        $this->assertSame('DELIVERY', $message->getApi());
        $this->assertSame(0.5, $message->getDuration());

        $request = $message->getRequest();
        $this->assertSame('POST', $request->getMethod());
        $this->assertSame('http://www.example.com/', (string) $request->getUri());
        $this->assertSame('Bearer <accessToken>', $request->getHeaderLine('Authorization'));
        $this->assertSame('{"message":"Hello!"}', (string) $request->getBody());

        $response = $message->getResponse();
        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame([], $response->getHeaders());
        $this->assertSame('{"message":"Hello to who?"}', (string) $response->getBody());
    }

    /**
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage String passed to Message::createFromString() is valid JSON but does not contain required fields.
     */
    public function testCreateFromStringInvalid()
    {
        Message::createFromString('{}');
    }

    public function testSerialization()
    {
        $request = new Request(
            'GET',
            'http://www.example.com/',
            ['Authorization' => 'Bearer <accessToken>']
        );
        $response = new Response(204, [
            'X-Contentful-Request-Id' => 'ddbaaceaced126fc7d29a4d8335f06d9',
        ]);

        $exception = null;
        $closure1 = function ($closure) {
            $closure();
        };
        $closure2 = function () use (&$exception, $request, $response) {
            $requestException = new RequestException('Not Found', $request, $response);
            $exception = new Exception($requestException, 'Error message');
        };
        $closure1($closure2);

        $message = new Message('DELIVERY', 0, $request, $response, $exception);
        $serialized = \unserialize(\serialize($message));

        $this->assertInstanceOf(RequestException::class, $message->getException()->getPrevious());
        $this->assertNull($serialized->getException()->getPrevious());

        $this->assertSame('DELIVERY', $serialized->getApi());
        $this->assertSame(0, $serialized->getDuration());
        $this->assertSame('Error message', $serialized->getException()->getMessage());
        $this->assertSame('ddbaaceaced126fc7d29a4d8335f06d9', $message->getException()->getRequestId());
        $this->assertSame('ddbaaceaced126fc7d29a4d8335f06d9', $serialized->getException()->getRequestId());

        // PSR-7 message bodies are mutable.
        // Because of this, it's easier to simply check for the string representation
        // of request and response objects, rather than actually checking all their properties.
        $this->assertSame(\GuzzleHttp\Psr7\str($serialized->getRequest()), \GuzzleHttp\Psr7\str($message->getRequest()));
        $this->assertSame(\GuzzleHttp\Psr7\str($serialized->getResponse()), \GuzzleHttp\Psr7\str($message->getResponse()));
    }
}
