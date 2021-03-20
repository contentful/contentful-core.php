<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2021 Contentful GmbH
 * @license   MIT
 */

declare(strict_types=1);

namespace Contentful\Tests\Core\Unit\Api;

use Contentful\Core\Api\Exception;
use Contentful\Core\Api\Message;
use Contentful\Tests\TestCase;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use function GuzzleHttp\Psr7\str as guzzle_stringify_message;

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

        $message = new Message('DELIVERY', 0.5, $request, $response, $exception);

        $this->assertSame('DELIVERY', $message->getApi());
        $this->assertSame(0.5, $message->getDuration());
        $this->assertSame($request, $message->getRequest());
        $this->assertSame($response, $message->getResponse());
        $this->assertInstanceOf(\Exception::class, $message->getException());

        $this->assertSame('error', $message->getLogLevel());
        $this->assertTrue($message->isError());

        // Exceptions and their stack traces are a bit brittle to test,
        // so message.json might need to be updated from time to time.
        $this->assertJsonFixtureEqualsJsonObject('message.json', $message);
        $this->assertJsonFixtureEqualsJsonString('message.json', (string) $message);
        $this->assertJsonFixtureEqualsJsonString('message.json', $message->asString());
    }

    public function testInvalidApi()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown API value "INVALID_API".');
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

    public function testCreateFromStringInvalid()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('String passed to Message::createFromString() is valid JSON but does not contain required fields.');
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

        $exception = \null;
        $closure1 = function ($closure) {
            $closure();
        };
        $closure2 = function () use (&$exception, $request, $response) {
            $requestException = new RequestException('Not Found', $request, $response);
            $exception = new Exception($requestException, 'Error message');
        };
        $closure1($closure2);

        $message = new Message('DELIVERY', 0.0, $request, $response, $exception);
        $serialized = \unserialize(\serialize($message));

        $this->assertInstanceOf(RequestException::class, $message->getException()->getPrevious());
        $this->assertNull($serialized->getException()->getPrevious());

        $this->assertSame('DELIVERY', $serialized->getApi());
        $this->assertSame(0.0, $serialized->getDuration());
        $this->assertSame('Error message', $serialized->getException()->getMessage());
        $this->assertSame('ddbaaceaced126fc7d29a4d8335f06d9', $message->getException()->getRequestId());
        $this->assertSame('ddbaaceaced126fc7d29a4d8335f06d9', $serialized->getException()->getRequestId());

        // PSR-7 message bodies are mutable.
        // Because of this, it's easier to simply check for the string representation
        // of request and response objects, rather than actually checking all their properties.
        $this->assertSame(guzzle_stringify_message($serialized->getRequest()), guzzle_stringify_message($message->getRequest()));
        $this->assertSame(guzzle_stringify_message($serialized->getResponse()), guzzle_stringify_message($message->getResponse()));
    }
}
