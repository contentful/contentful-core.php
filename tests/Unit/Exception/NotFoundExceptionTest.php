<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2024 Contentful GmbH
 * @license   MIT
 */

declare(strict_types=1);

namespace Contentful\Tests\Core\Unit\Exception;

use Contentful\Core\Exception\NotFoundException;
use Contentful\Tests\TestCase;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class NotFoundExceptionTest extends TestCase
{
    public function testExceptionStructure()
    {
        $request = new Request('GET', 'https://cdn.contentful.com/spaces/cfexampleapi/entries/not-existing');
        $response = new Response(
            404,
            ['X-Contentful-Request-Id' => 'db2d795acb78e0592af00759986c744b'],
            '{"sys": {"type": "Error","id": "NotFound"},"message": "The resource could not be found.","details": {"type": "Entry","id": "not-existing","space": "cfexampleapi"},"requestId": "db2d795acb78e0592af00759986c744b"}',
            '1.1',
            'Not Found'
        );

        $guzzleException = new ClientException('This is an error', $request, $response);

        $exception = new NotFoundException($guzzleException);

        $this->assertTrue($exception->hasResponse());
        $this->assertSame($request, $exception->getRequest());
        $this->assertSame($response, $exception->getResponse());
        $this->assertSame('db2d795acb78e0592af00759986c744b', $exception->getRequestId());
        $this->assertSame('The resource could not be found.', $exception->getMessage());
    }
}
