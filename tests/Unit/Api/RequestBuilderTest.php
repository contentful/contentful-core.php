<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2025 Contentful GmbH
 * @license   MIT
 */

declare(strict_types=1);

namespace Contentful\Tests\Core\Unit\Exception;

use Contentful\Core\Api\RequestBuilder;
use Contentful\Core\Api\UserAgentGenerator;
use Contentful\Tests\TestCase;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;

class RequestBuilderTest extends TestCase
{
    /**
     * @var RequestBuilder
     */
    private $requestBuilder;

    protected function setUp(): void
    {
        $this->requestBuilder = new RequestBuilder(
            '<accessToken>',
            'https://www.example.com',
            'application/json',
            new UserAgentGenerator('contentful-core.php', '1.0')
        );
    }

    /**
     * @dataProvider requestProvider
     *
     * @param string   $method
     * @param string   $path
     * @param string[] $options
     * @param array    $headers
     */
    public function testBuilder($method, $path, $options, RequestInterface $request, $headers = [])
    {
        $builtRequest = $this->requestBuilder->build($method, $path, $options);

        $this->assertSame($request->getMethod(), $builtRequest->getMethod());
        $this->assertSame((string) $request->getUri(), (string) $builtRequest->getUri());
        $this->assertSame((string) $request->getBody(), (string) $builtRequest->getBody());

        $this->assertSame('application/json', $builtRequest->getHeaderLine('Accept'));
        $this->assertSame('gzip', $builtRequest->getHeaderLine('Accept-Encoding'));
        $this->assertSame('Bearer <accessToken>', $builtRequest->getHeaderLine('Authorization'));
        $this->assertRegExp(
            '/^sdk contentful-core.php\/[0-9\.]*(-(dev|beta|alpha|RC))?; platform PHP\/[0-9\.]*; os (Windows|Linux|macOS);$/',
            $builtRequest->getHeaderLine('X-Contentful-User-Agent')
        );

        foreach ($headers as $key => $value) {
            $this->assertSame($value, $builtRequest->getHeaderLine($key));
        }
    }

    public function requestProvider()
    {
        yield ['GET', '/', [], new Request('GET', 'https://www.example.com/')];

        yield ['GET', '/some-path', ['query' => ['someParam' => 'someValue']], new Request('GET', 'https://www.example.com/some-path?someParam=someValue')];

        yield ['POST', '/another-path', [
            'query' => ['anotherParam' => 'anotherValue'],
            'host' => 'https://www.google.com',
            'headers' => ['X-Contentful-Version' => '1'],
            'body' => '{"message": "Hello, world!"}',
        ], new Request('POST', 'https://www.google.com/another-path?anotherParam=anotherValue', [], '{"message": "Hello, world!"}'), [
            'X-Contentful-Version' => '1',
        ]];
    }
}
