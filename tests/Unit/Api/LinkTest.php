<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2018 Contentful GmbH
 * @license   MIT
 */

namespace Contentful\Tests\Core\Unit\Api;

use Contentful\Core\Api\Link;
use Contentful\Tests\Core\TestCase;

class LinkTest extends TestCase
{
    public function testGetter()
    {
        $link = new Link('123', 'Entry');

        $this->assertSame('123', $link->getId());
        $this->assertSame('Entry', $link->getLinkType());
    }

    public function testJsonSerialize()
    {
        $link = new Link('123', 'Entry');

        $this->assertJsonStringEqualsJsonString('{"sys": {"type": "Link", "id": "123", "linkType": "Entry"}}', \json_encode($link));
    }
}
