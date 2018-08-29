<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2018 Contentful GmbH
 * @license   MIT
 */

namespace Contentful\Tests\Core\Unit\Api;

use Contentful\Core\Api\BaseQuery;
use Contentful\Core\Api\DateTimeImmutable;
use Contentful\Core\Api\Location;
use Contentful\Tests\Core\TestCase;

class BaseQueryTest extends TestCase
{
    public function testFilterWithNoOptions()
    {
        $query = new ConcreteQuery();

        $this->assertSame('', $query->getQueryString());
    }

    public function testFilterWithLimit()
    {
        $query = (new ConcreteQuery())
            ->setLimit(150)
        ;

        $this->assertSame('limit=150', $query->getQueryString());
    }

    /**
     * @expectedException        \RangeException
     * @expectedExceptionMessage Limit value must be between 0 and 1000, "1500" given.
     */
    public function testLimitThrowsOnValueTooLarge()
    {
        (new ConcreteQuery())
            ->setLimit(1500)
        ;
    }

    /**
     * @expectedException        \RangeException
     * @expectedExceptionMessage Limit value must be between 0 and 1000, "0" given.
     */
    public function testLimitThrowsOnValueZero()
    {
        (new ConcreteQuery())
            ->setLimit(0)
        ;
    }

    /**
     * @expectedException        \RangeException
     * @expectedExceptionMessage Limit value must be between 0 and 1000, "-1" given.
     */
    public function testLimitThrowsOnValueNegative()
    {
        (new ConcreteQuery())
            ->setLimit(-1)
        ;
    }

    public function testLimitSetNull()
    {
        $query = (new ConcreteQuery())
            ->setLimit(150)
        ;

        $query->setLimit(\null);

        $this->assertSame('', $query->getQueryString());
    }

    public function testFilterWithSkip()
    {
        $query = (new ConcreteQuery())
            ->setSkip(10)
        ;

        $this->assertSame('skip=10', $query->getQueryString());
    }

    /**
     * @expectedException        \RangeException
     * @expectedExceptionMessage Skip value must be 0 or bigger, "-1" given.
     */
    public function testSkipThrowsOnValueNegative()
    {
        (new ConcreteQuery())
            ->setSkip(-1)
        ;
    }

    public function testFilterOrderBy()
    {
        $query = (new ConcreteQuery())
            ->orderBy('sys.createdAt')
        ;

        $this->assertSame('order=sys.createdAt', $query->getQueryString());
    }

    public function testFilterOrderByReversed()
    {
        $query = (new ConcreteQuery())
            ->orderBy('sys.createdAt', \true)
        ;

        $this->assertSame('order=-sys.createdAt', $query->getQueryString());
    }

    public function testFilterOrderByMultiple()
    {
        $query = (new ConcreteQuery())
            ->orderBy('sys.createdAt')
            ->orderBy('sys.updatedAt', \true)
        ;

        $this->assertSame('order=sys.createdAt%2C-sys.updatedAt', $query->getQueryString());
    }

    public function testFilterByContentType()
    {
        $query = (new ConcreteQuery())
            ->setContentType('cat')
        ;

        $this->assertSame('content_type=cat', $query->getQueryString());
    }

    public function testWhere()
    {
        $query = (new ConcreteQuery())
            ->where('sys.id', 'nyancat')
        ;

        $this->assertSame('sys.id=nyancat', $query->getQueryString());
    }

    public function testWhereOperator()
    {
        $query = (new ConcreteQuery())
            ->where('sys.id', 'nyancat', 'ne')
        ;

        $this->assertSame('sys.id%5Bne%5D=nyancat', $query->getQueryString());
    }

    public function testWhereDateTime()
    {
        $query = (new ConcreteQuery())
            ->where('sys.updatedAt', new DateTimeImmutable('2013-01-01T00:00:00Z'), 'lte')
        ;

        $this->assertSame('sys.updatedAt%5Blte%5D=2013-01-01T00%3A00%3A00%2B00%3A00', $query->getQueryString());
    }

    public function testWhereDateTimeResetsSeconds()
    {
        $query = (new ConcreteQuery())
            ->where('sys.updatedAt', new DateTimeImmutable('2013-01-01T12:30:35Z'), 'lte')
        ;

        $this->assertSame('sys.updatedAt%5Blte%5D=2013-01-01T12%3A30%3A00%2B00%3A00', $query->getQueryString());
    }

    public function testWhereLocation()
    {
        $query = (new ConcreteQuery())
            ->where('fields.center', new Location(15.0, 17.8), 'near')
        ;

        $this->assertSame('fields.center%5Bnear%5D=15%2C17.8', $query->getQueryString());
    }

    public function testWhereArray()
    {
        $query = (new ConcreteQuery())
            ->where('fields.favoriteColor', ['blue', 'red'], 'all')
        ;

        $this->assertSame('fields.favoriteColor%5Ball%5D=blue%2Cred', $query->getQueryString());
    }

    /**
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage Unknown operator "wrong" given. Expected "ne, all, in, nin, exists, lt, lte, gt, gte, match, near, within" or null.
     */
    public function testWhereUnknownOperator()
    {
        (new ConcreteQuery())
            ->where('sys.id', 'nyancat', 'wrong')
        ;
    }

    /**
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage Unknown MIME-type group "invalid" given. Expected "attachment, plaintext, image, audio, video, richtext, presentation, spreadsheet, pdfdocument, archive, code, markup" or null.
     */
    public function testSetMimeTypeGroupInvalid()
    {
        (new ConcreteQuery())
            ->setMimeTypeGroup('invalid')
        ;
    }

    public function testFilterByMimeTypeGroup()
    {
        $query = (new ConcreteQuery())
            ->setMimeTypeGroup('image')
        ;

        $this->assertSame('mimetype_group=image', $query->getQueryString());
    }

    public function testFilterCombined()
    {
        $query = new ConcreteQuery();
        $query
            ->setContentType('cat')
            ->setLimit(150)
            ->setSkip(10)
            ->orderBy('sys.createdAt')
            ->where('sys.id', 'nyancat')
            ->where('sys.updatedAt', new DateTimeImmutable('2013-01-01T00:00:00Z'), 'lte')
        ;

        $this->assertSame(
            'sys.id=nyancat&sys.updatedAt%5Blte%5D=2013-01-01T00%3A00%3A00%2B00%3A00&limit=150&skip=10&content_type=cat&order=sys.createdAt',
            $query->getQueryString()
        );
    }

    public function testQueryWithSelect()
    {
        $query = (new ConcreteQuery())
            ->select(['foobar1'])
            ->setContentType('cat')
        ;

        $this->assertSame('content_type=cat&select=sys%2Cfoobar1', $query->getQueryString());

        $query = (new ConcreteQuery())
            ->select(['foobar2'])
            ->setContentType('cat')
        ;

        $this->assertSame('content_type=cat&select=sys%2Cfoobar2', $query->getQueryString());

        $query = (new ConcreteQuery())
            ->select(['sys'])
            ->setContentType('cat')
        ;

        $this->assertSame('content_type=cat&select=sys', $query->getQueryString());
    }

    public function testIncomingLinks()
    {
        $query = (new ConcreteQuery())
            ->linksToEntry('entryId')
        ;

        $this->assertSame('links_to_entry=entryId', $query->getQueryString());

        $query = (new ConcreteQuery())
            ->linksToAsset('assetId')
        ;

        $this->assertSame('links_to_asset=assetId', $query->getQueryString());
    }
}

class ConcreteQuery extends BaseQuery
{
}
