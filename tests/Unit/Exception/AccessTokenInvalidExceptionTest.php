<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2022 Contentful GmbH
 * @license   MIT
 */

declare(strict_types=1);

namespace Contentful\Tests\Core\Unit\Exception;

use Contentful\Core\Exception\AccessTokenInvalidException;
use Contentful\Tests\TestCase;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class AccessTokenInvalidExceptionTest extends TestCase
{
    public function testExceptionStructure()
    {
        $request = new Request('GET', 'https://cdn.contentful.com/spaces/cfexampleapi/entries');
        $response = new Response(
            401,
            ['X-Contentful-Request-Id' => '426753a1639d40c23ad4cbf085a072c7'],
            '{"sys": {"type": "Error","id": "AccessTokenInvalid"},"message": "The access token you sent could not be found or is invalid.","requestId":"426753a1639d40c23ad4cbf085a072c7"}',
            1.1,
            'Unauthorized'
        );

        $guzzleException = new ClientException('This is an error', $request, $response);

        $exception = new AccessTokenInvalidException($guzzleException);

        $this->assertTrue($exception->hasResponse());
        $this->assertSame($request, $exception->getRequest());
        $this->assertSame($response, $exception->getResponse());
        $this->assertSame('426753a1639d40c23ad4cbf085a072c7', $exception->getRequestId());
        $this->assertSame('The access token you sent could not be found or is invalid.', $exception->getMessage());
    }
}
