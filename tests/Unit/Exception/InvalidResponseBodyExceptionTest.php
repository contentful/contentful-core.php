<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2020 Contentful GmbH
 * @license   MIT
 */

declare(strict_types=1);

namespace Contentful\Tests\Core\Unit\Exception;

use Contentful\Core\Api\Requester;
use Contentful\Core\Exception\InvalidResponseBodyException;
use Contentful\Tests\TestCase;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;

class InvalidResponseBodyExceptionTest extends TestCase
{
    public function testInvalidResponseException()
    {
        $response = new Response(200, [], '{}');
        $handler = function (RequestInterface $request, array $options) use ($response) {
            return $response;
        };
        $httpClient = $this->createHttpClient($handler);
        $requester = new Requester($httpClient, 'DELIVERY');

        $request = new Request('GET', 'https://www.example.com/some-page');
        $message = $requester->sendRequest($request);
        $this->assertInstanceOf(InvalidResponseBodyException::class, $message->getException());
    }

    public function createHttpClient(callable $handlerOverride = \null)
    {
        $stack = new HandlerStack();
        $stack->setHandler(new CurlHandler());
        $stack->push(function (callable $handler) use ($handlerOverride) {
            return function (RequestInterface $request, array $options) use ($handler, $handlerOverride) {
                throw new ClientException('Foo', $request, new Response(500, [], 'FOO'));
            };
        });

        return new HttpClient(['handler' => $stack]);
    }
}
