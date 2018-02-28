<?php

/**
 * This file is part of the contentful-core.php package.
 *
 * @copyright 2015-2018 Contentful GmbH
 * @license   MIT
 */

namespace Contentful\Tests\Core\Unit\Exception;

use Contentful\Core\Exception\BadRequestException;
use Contentful\Tests\Core\TestCase;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class BadRequestExceptionTest extends TestCase
{
    public function testExceptionStructure()
    {
        $request = new Request('GET', 'https://cdn.contentful.com/spaces/cfexampleapi/entries');
        $response = new Response(
            401,
            ['X-Contentful-Request-Id' => '42b6dc50b0c619539268ac1c72da51e8'],
            '{"sys":{"type":"Error","id":"BadRequest"},"message":"Unknown locale: invalidLocale","requestId": "42b6dc50b0c619539268ac1c72da51e8"}'
        );

        $guzzleException = new ClientException('This is an error', $request, $response);

        $exception = new BadRequestException($guzzleException);

        $this->assertTrue($exception->hasResponse());
        $this->assertSame($request, $exception->getRequest());
        $this->assertSame($response, $exception->getResponse());
        $this->assertSame('42b6dc50b0c619539268ac1c72da51e8', $exception->getRequestId());
        $this->assertSame('Unknown locale: invalidLocale', $exception->getMessage());
    }
}
