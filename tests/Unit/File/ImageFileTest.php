<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2020 Contentful GmbH
 * @license   MIT
 */

declare(strict_types=1);

namespace Contentful\Tests\Core\Unit\File;

use Contentful\Core\File\ImageFile;
use Contentful\Core\File\ImageOptions;
use Contentful\Tests\TestCase;

class ImageFileTest extends TestCase
{
    /**
     * @var ImageFile
     */
    protected $file;

    public function setUp()
    {
        $this->file = new ImageFile(
            'Nyan_cat_250px_frame.png',
            'image/png',
            '//images.contentful.com/cfexampleapi/4gp6taAwW4CmSgumq2ekUm/9da0cd1936871b8d72343e895a00d611/Nyan_cat_250px_frame.png',
            12273,
            250,
            250
        );
    }

    public function testGetter()
    {
        $this->assertSame('//images.contentful.com/cfexampleapi/4gp6taAwW4CmSgumq2ekUm/9da0cd1936871b8d72343e895a00d611/Nyan_cat_250px_frame.png', $this->file->getUrl());
        $this->assertSame(250, $this->file->getWidth());
        $this->assertSame(250, $this->file->getHeight());
    }

    public function testWithImageOptions()
    {
        $stub = $this->getMockBuilder(ImageOptions::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $stub->method('getQueryString')
            ->willReturn('fm=jpg&q=50')
        ;

        $this->assertSame(
            '//images.contentful.com/cfexampleapi/4gp6taAwW4CmSgumq2ekUm/9da0cd1936871b8d72343e895a00d611/Nyan_cat_250px_frame.png?fm=jpg&q=50',
            $this->file->getUrl($stub)
        );
    }

    public function testJsonSerialize()
    {
        $this->assertJsonFixtureEqualsJsonObject('serialized.json', $this->file);
    }
}
