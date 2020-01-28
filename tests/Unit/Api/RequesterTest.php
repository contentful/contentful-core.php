<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2020 Contentful GmbH
 * @license   MIT
 */

declare(strict_types=1);

namespace Contentful\Tests\Core\Unit\Api;

use Contentful\Core\Api\Requester;
use Contentful\Tests\TestCase;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;

class RequesterTest extends TestCase
{
    public function createHttpClient(callable $handlerOverride = \null)
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

    public function testRequester()
    {
        $response = new Response(200, [], '{}');
        $handler = function (RequestInterface $request, array $options) use ($response) {
            return $response;
        };
        $httpClient = $this->createHttpClient($handler);
        $requester = new Requester($httpClient, 'DELIVERY');

        $request = new Request('GET', 'https://www.example.com/some-page');
        $message = $requester->sendRequest($request);

        $this->assertSame($request, $message->getRequest());
        $this->assertSame($response, $message->getResponse());
        $this->assertNull($message->getException());
        $this->assertFalse($message->isError());
        $this->assertGreaterThanOrEqual(0, $message->getDuration());
    }
}
