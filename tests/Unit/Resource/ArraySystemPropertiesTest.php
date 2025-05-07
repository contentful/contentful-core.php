<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2025 Contentful GmbH
 * @license   MIT
 */

declare(strict_types=1);

namespace Contentful\Tests\Core\Unit\Resource;

use Contentful\Core\Resource\ArraySystemProperties;
use Contentful\Tests\TestCase;

class ArraySystemPropertiesTest extends TestCase
{
    public function testGetData()
    {
        $sys = new ArraySystemProperties([]);

        $this->assertSame('Array', $sys->getType());
        $this->assertSame(['type' => 'Array'], $sys->jsonSerialize());
    }

    public function testGetIdThrowsException()
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Resource of type Array does not have an ID.');

        (new ArraySystemProperties([]))
            ->getId()
        ;
    }
}
