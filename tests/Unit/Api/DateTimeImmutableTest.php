<?php

/**
 * This file is part of the contentful-core.php package.
 *
 * @copyright 2015-2018 Contentful GmbH
 * @license   MIT
 */

namespace Contentful\Tests\Core\Unit\Api;

use Contentful\Core\Api\DateTimeImmutable;
use Contentful\Tests\Core\TestCase;

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
    }
}
