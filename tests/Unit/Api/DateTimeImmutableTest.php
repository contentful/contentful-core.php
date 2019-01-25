<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2019 Contentful GmbH
 * @license   MIT
 */

declare(strict_types=1);

namespace Contentful\Tests\Core\Unit\Api;

use Contentful\Core\Api\DateTimeImmutable;
use Contentful\Tests\TestCase;
use function GuzzleHttp\json_encode as guzzle_json_encode;

class DateTimeImmutableTest extends TestCase
{
    public function testStringRepresentation()
    {
        $date = new DateTimeImmutable('1999-01-05T23:15:00.199Z');

        $this->assertSame('1999-01-05T23:15:00.199Z', $date->formatForJson());
        $this->assertSame('1999-01-05T23:15:00.199Z', (string) $date);

        $date = new DateTimeImmutable('September 19th, 1988');

        $this->assertSame('1988-09-19T00:00:00Z', $date->formatForJson());
        $this->assertSame('1988-09-19T00:00:00Z', (string) $date);
        $this->assertSame('"1988-09-19T00:00:00Z"', guzzle_json_encode($date));
    }
}
