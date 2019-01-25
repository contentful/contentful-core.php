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
use Contentful\Core\Api\Location;
use Contentful\Tests\Core\Implementation\Query;
use Contentful\Tests\TestCase;

class BaseQueryTest extends TestCase
{
    public function testFilterWithNoOptions()
    {
        $query = new Query();

        $this->assertSame('', $query->getQueryString());
    }

    public function testFilterWithLimit()
    {
        $query = (new Query())
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
        (new Query())
            ->setLimit(1500)
        ;
    }

    /**
     * @expectedException        \RangeException
     * @expectedExceptionMessage Limit value must be between 0 and 1000, "0" given.
     */
    public function testLimitThrowsOnValueZero()
    {
        (new Query())
            ->setLimit(0)
        ;
    }

    /**
     * @expectedException        \RangeException
     * @expectedExceptionMessage Limit value must be between 0 and 1000, "-1" given.
     */
    public function testLimitThrowsOnValueNegative()
    {
        (new Query())
            ->setLimit(-1)
        ;
    }

    public function testLimitSetNull()
    {
        $query = (new Query())
            ->setLimit(150)
        ;

        $query->setLimit(\null);

        $this->assertSame('', $query->getQueryString());
    }

    public function testFilterWithSkip()
    {
        $query = (new Query())
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
        (new Query())
            ->setSkip(-1)
        ;
    }

    public function testFilterOrderBy()
    {
        $query = (new Query())
            ->orderBy('sys.createdAt')
        ;

        $this->assertSame('order=sys.createdAt', $query->getQueryString());
    }

    public function testFilterOrderByReversed()
    {
        $query = (new Query())
            ->orderBy('sys.createdAt', \true)
        ;

        $this->assertSame('order=-sys.createdAt', $query->getQueryString());
    }

    public function testFilterOrderByMultiple()
    {
        $query = (new Query())
            ->orderBy('sys.createdAt')
            ->orderBy('sys.updatedAt', \true)
        ;

        $this->assertSame('order=sys.createdAt%2C-sys.updatedAt', $query->getQueryString());
    }

    public function testFilterByContentType()
    {
        $query = (new Query())
            ->setContentType('cat')
        ;

        $this->assertSame('content_type=cat', $query->getQueryString());
    }

    public function testWhere()
    {
        $query = (new Query())
            ->where('sys.id', 'nyancat')
        ;

        $this->assertSame('sys.id=nyancat', $query->getQueryString());
    }

    public function testWhereOperator()
    {
        $query = (new Query())
            ->where('sys.id[ne]', 'nyancat')
        ;

        $this->assertSame('sys.id%5Bne%5D=nyancat', $query->getQueryString());
    }

    public function testWhereDateTime()
    {
        $query = (new Query())
            ->where('sys.updatedAt[lte]', new DateTimeImmutable('2013-01-01T00:00:00Z'))
        ;

        $this->assertSame('sys.updatedAt%5Blte%5D=2013-01-01T00%3A00%3A00%2B00%3A00', $query->getQueryString());
    }

    public function testWhereDateTimeResetsSeconds()
    {
        $query = (new Query())
            ->where('sys.updatedAt[lte]', new DateTimeImmutable('2013-01-01T12:30:35Z'))
        ;

        $this->assertSame('sys.updatedAt%5Blte%5D=2013-01-01T12%3A30%3A00%2B00%3A00', $query->getQueryString());
    }

    public function testWhereLocation()
    {
        $query = (new Query())
            ->where('fields.center[near]', new Location(15.0, 17.8))
        ;

        $this->assertSame('fields.center%5Bnear%5D=15%2C17.8', $query->getQueryString());
    }

    public function testWhereArray()
    {
        $query = (new Query())
            ->where('fields.favoriteColor[all]', ['blue', 'red'])
        ;

        $this->assertSame('fields.favoriteColor%5Ball%5D=blue%2Cred', $query->getQueryString());
    }

    /**
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage Unknown operator "wrong" given. Expected "ne, all, in, nin, exists, lt, lte, gt, gte, match, near, within" or no operator.
     */
    public function testWhereUnknownOperator()
    {
        (new Query())
            ->where('sys.id[wrong]', 'nyancat')
        ;
    }

    /**
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage Unknown MIME-type group "invalid" given. Expected "attachment, plaintext, image, audio, video, richtext, presentation, spreadsheet, pdfdocument, archive, code, markup" or null.
     */
    public function testSetMimeTypeGroupInvalid()
    {
        (new Query())
            ->setMimeTypeGroup('invalid')
        ;
    }

    public function testFilterByMimeTypeGroup()
    {
        $query = (new Query())
            ->setMimeTypeGroup('image')
        ;

        $this->assertSame('mimetype_group=image', $query->getQueryString());
    }

    public function testFilterCombined()
    {
        $query = new Query();
        $query
            ->setContentType('cat')
            ->setLimit(150)
            ->setSkip(10)
            ->orderBy('sys.createdAt')
            ->where('sys.id', 'nyancat')
            ->where('sys.updatedAt[lte]', new DateTimeImmutable('2013-01-01T00:00:00Z'))
        ;

        $this->assertSame(
            'sys.id=nyancat&sys.updatedAt%5Blte%5D=2013-01-01T00%3A00%3A00%2B00%3A00&limit=150&skip=10&content_type=cat&order=sys.createdAt',
            $query->getQueryString()
        );
    }

    public function testQueryWithSelect()
    {
        $query = (new Query())
            ->select(['foobar1'])
            ->setContentType('cat')
        ;

        $this->assertSame('content_type=cat&select=foobar1%2Csys', $query->getQueryString());

        $query = (new Query())
            ->select(['foobar2'])
            ->setContentType('cat')
        ;

        $this->assertSame('content_type=cat&select=foobar2%2Csys', $query->getQueryString());

        $query = (new Query())
            ->select(['sys'])
            ->setContentType('cat')
        ;

        $this->assertSame('content_type=cat&select=sys', $query->getQueryString());
    }

    public function testIncomingLinks()
    {
        $query = (new Query())
            ->linksToEntry('entryId')
        ;

        $this->assertSame('links_to_entry=entryId', $query->getQueryString());

        $query = (new Query())
            ->linksToAsset('assetId')
        ;

        $this->assertSame('links_to_asset=assetId', $query->getQueryString());
    }
}
