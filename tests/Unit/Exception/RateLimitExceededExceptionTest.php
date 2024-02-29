<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2024 Contentful GmbH
 * @license   MIT
 */

declare(strict_types=1);

namespace Contentful\Tests\Core\Unit\Exception;

use Contentful\Core\Exception\RateLimitExceededException;
use Contentful\Tests\TestCase;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class RateLimitExceededExceptionTest extends TestCase
{
    public function testExceptionStructure()
    {
        $request = new Request('GET', 'https://preview.contentful.com/spaces/bc32cj3kyfet/entries?limit=6');
        $response = new Response(
            429,
            [
                'X-Contentful-Request-Id' => 'db2d795acb78e0592af00759986c744b',
                'X-Contentful-RateLimit-Reset' => '2727',
            ],
            '{"sys": {"type": "Error","id": "RateLimitExceeded"},"message": "You have exceeded the rate limit of the Organization this Space belongs to by making too many API requests within a short timespan. Please wait a moment before trying the request again.","requestId": "4d0274fb176b51ae43a64b98639a3090"}',
            '1.1',
            ''
        );

        $guzzleException = new ClientException('This is an error', $request, $response);

        $exception = new RateLimitExceededException($guzzleException);

        $this->assertTrue($exception->hasResponse());
        $this->assertSame($request, $exception->getRequest());
        $this->assertSame($response, $exception->getResponse());
        $this->assertSame('db2d795acb78e0592af00759986c744b', $exception->getRequestId());
        $this->assertSame('You have exceeded the rate limit of the Organization this Space belongs to by making too many API requests within a short timespan. Please wait a moment before trying the request again.', $exception->getMessage());
        $this->assertSame(2727, $exception->getRateLimitReset());
    }
}
