<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2019 Contentful GmbH
 * @license   MIT
 */

declare(strict_types=1);

namespace Contentful\Tests\Core\Unit\Resource;

use Contentful\Core\Resource\ArraySystemProperties;
use Contentful\Core\Resource\ResourceArray;
use Contentful\Tests\Core\Implementation\Resource;
use Contentful\Tests\TestCase;

class ResourceArrayTest extends TestCase
{
    public function testGetSet()
    {
        $array = new ResourceArray(['abc'], 10, 2, 0);

        $this->assertSame(10, $array->getTotal());
        $this->assertSame(2, $array->getLimit());
        $this->assertSame(0, $array->getSkip());

        $this->assertSame('Array', $array->getType());

        $this->assertInstanceOf(ArraySystemProperties::class, $array->getSystemProperties());
    }

    /**
     * @expectedException        \LogicException
     * @expectedExceptionMessage Resource of type Array can not be represented as a Link object.
     */
    public function testArrayCantBeConvertedToLink()
    {
        (new ResourceArray([], 1, 0, 0))
            ->asLink()
        ;
    }

    /**
     * @expectedException        \LogicException
     * @expectedExceptionMessage Resource of type Array does not have an ID.
     */
    public function testArrayDoesNotHaveAnId()
    {
        (new ResourceArray([], 1, 0, 0))
            ->getId()
        ;
    }

    public function testCountable()
    {
        $array = new ResourceArray(['abc'], 10, 2, 0);

        $this->assertInstanceOf('\Countable', $array);
        $this->assertCount(1, $array);
    }

    public function testArrayAccess()
    {
        $resource = new Resource('resourceId', 'resourceType', 'Some title');
        $array = new ResourceArray([$resource], 10, 2, 0);

        $this->assertInstanceOf('\Countable', $array);
        $this->assertTrue(isset($array[0]));
        $this->assertSame($resource, $array[0]);
    }

    public function testJsonSerializeEmpty()
    {
        $array = new ResourceArray([], 0, 10, 0);

        $this->assertJsonFixtureEqualsJsonObject('serialized.json', $array);
    }

    public function testIsIterable()
    {
        $array = new ResourceArray([], 10, 2, 0);

        $this->assertInstanceOf('\Traversable', $array);
    }

    public function testIteration()
    {
        $resource = new Resource('resourceId', 'resourceType', 'Some title');
        $array = new ResourceArray([$resource, $resource], 10, 2, 0);
        $count = 0;

        foreach ($array as $key => $elem) {
            ++$count;
            $this->assertSame($array[$key], $elem);
        }

        $this->assertSame(2, $count);
    }

    public function testGetItems()
    {
        $array = new ResourceArray(['abc', 'def'], 10, 2, 0);

        $this->assertSame(['abc', 'def'], $array->getItems());
    }

    /**
     * @expectedException        \BadMethodCallException
     * @expectedExceptionMessage "Contentful\Core\Resource\ResourceArray" is read-only.
     */
    public function testOffsetSetThrows()
    {
        $array = new ResourceArray([], 0, 2, 0);

        $array[0] = 'abc';
    }

    /**
     * @expectedException        \BadMethodCallException
     * @expectedExceptionMessage "Contentful\Core\Resource\ResourceArray" is read-only.
     */
    public function testOffsetUnsetThrows()
    {
        $array = new ResourceArray(['abc'], 10, 2, 0);

        unset($array[0]);
    }
}
