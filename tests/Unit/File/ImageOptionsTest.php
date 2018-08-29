<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2018 Contentful GmbH
 * @license   MIT
 */

namespace Contentful\Tests\Core\Unit\File;

use Contentful\Core\File\ImageOptions;
use Contentful\Tests\Core\TestCase;

class ImageOptionsTest extends TestCase
{
    public function testNoOptions()
    {
        $options = new ImageOptions();

        $this->assertSame('', $options->getQueryString());
    }

    public function tesSetWidthNull()
    {
        $options = new ImageOptions();
        $options->setWidth(\null);

        $this->assertSame('', $options->getQueryString());
    }

    /**
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage Width must not be negative.
     */
    public function testSetWidthNegative()
    {
        $options = new ImageOptions();
        $options->setWidth(-50);
    }

    public function testQueryWidth()
    {
        $options = new ImageOptions();
        $options->setWidth(50);

        $this->assertSame('w=50', $options->getQueryString());
    }

    public function testSetHeightNull()
    {
        $options = new ImageOptions();
        $options->setHeight(\null);

        $this->assertSame('', $options->getQueryString());
    }

    /**
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage Height must not be negative.
     */
    public function testSetHeightNegative()
    {
        $options = new ImageOptions();
        $options->setHeight(-50);
    }

    public function testQueryHeight()
    {
        $options = new ImageOptions();
        $options->setHeight(50);

        $this->assertSame('h=50', $options->getQueryString());
    }

    public function testGetSetFormatNull()
    {
        $options = new ImageOptions();
        $options->setFormat(\null);

        $this->assertSame('', $options->getQueryString());
    }

    /**
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage Unknown format "invalid" given. Expected "png, jpg, webp" or null.
     */
    public function testSetFormatInvalid()
    {
        $options = new ImageOptions();
        $options->setFormat('invalid');
    }

    public function testQueryFormat()
    {
        $options = new ImageOptions();
        $options->setFormat('png');

        $this->assertSame('fm=png', $options->getQueryString());
    }

    public function testGetSetQualityNull()
    {
        $options = new ImageOptions();
        $options->setQuality(\null);

        $this->assertSame('', $options->getQueryString());
    }

    /**
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage Quality must be between 1 and 100, "-50" given.
     */
    public function testSetQualityNegative()
    {
        $options = new ImageOptions();
        $options->setQuality(-50);
    }

    public function testQueryQuality()
    {
        $options = new ImageOptions();
        $options->setQuality(50);

        $this->assertSame('fm=jpg&q=50', $options->getQueryString());
    }

    public function testQueryQualityOverridesFormat()
    {
        $options = (new ImageOptions())
            ->setFormat('png')
            ->setQuality(50)
        ;

        $this->assertSame('fm=jpg&q=50', $options->getQueryString());
    }

    public function testQueryProgressive()
    {
        $options = new ImageOptions();
        $options->setProgressive(\true);

        $this->assertSame('fm=jpg&fl=progressive', $options->getQueryString());
    }

    public function testQueryProgressiveOverridesFormat()
    {
        $options = (new ImageOptions())
            ->setFormat('png')
            ->setProgressive(\true)
        ;

        $this->assertSame('fm=jpg&fl=progressive', $options->getQueryString());
    }

    /**
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage Unknown resize fit "invalid" given. Expected "pad, crop, fill, thumb, scale" or null.
     */
    public function testSetResizeFitInvalid()
    {
        $options = new ImageOptions();
        $options->setResizeFit('invalid');
    }

    public function testQueryResizeFit()
    {
        $options = new ImageOptions();
        $options->setResizeFit('pad');

        $this->assertSame('fit=pad', $options->getQueryString());
    }

    public function testSetResizeFocus()
    {
        $options = (new ImageOptions())
            ->setResizeFocus('top')
        ;

        $this->assertSame('', $options->getQueryString());
    }

    /**
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage Unknown resize focus "invalid" given. Expected "face, faces, top, bottom, right, left, top_right, top_left, bottom_right, bottom_left" or null.
     */
    public function testSetResizeFocusInvalid()
    {
        $options = new ImageOptions();
        $options->setResizeFocus('invalid');
    }

    public function testQueryResizeFocus()
    {
        $options = (new ImageOptions())
            ->setResizeFit('thumb')
            ->setResizeFocus('top')
        ;

        $this->assertSame('fit=thumb&f=top', $options->getQueryString());
    }

    public function testQueryResizeFocusIgnoredWithoutFit()
    {
        $options = new ImageOptions();
        $options->setResizeFocus('top');

        $this->assertSame('', $options->getQueryString());
    }

    /**
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage Radius must not be negative.
     */
    public function testSetRadiusNegative()
    {
        $options = new ImageOptions();
        $options->setRadius(-13.2);
    }

    public function testQueryRadius()
    {
        $options = new ImageOptions();
        $options->setRadius(50.3);

        $this->assertSame('r=50.3', $options->getQueryString());
    }

    public function testGetSetBackgroundColorSixDigits()
    {
        $options = (new ImageOptions())
            ->setResizeFit('pad')
            ->setBackgroundColor('#a0f326')
        ;

        $this->assertSame('fit=pad&bg=rgb%3Aa0f326', $options->getQueryString());
    }

    public function testGetSetBackgroundColorThreeDigits()
    {
        $options = (new ImageOptions())
            ->setResizeFit('pad')
            ->setBackgroundColor('#0AF')
        ;

        $this->assertSame('fit=pad&bg=rgb%3A0AF', $options->getQueryString());
    }

    public function testGetSetBackgroundColorUpperCase()
    {
        $options = (new ImageOptions())
            ->setResizeFit('pad')
            ->setBackgroundColor('#A0F326')
        ;

        $this->assertSame('fit=pad&bg=rgb%3AA0F326', $options->getQueryString());
    }

    /**
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage Background color must be in hexadecimal format.
     */
    public function testSetBackgroundColorTooShort()
    {
        $options = new ImageOptions();
        $options->setBackgroundColor('#A0F36');
    }

    /**
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage Background color must be in hexadecimal format.
     */
    public function testSetBackgroundInvalidCharacter()
    {
        $options = new ImageOptions();
        $options->setBackgroundColor('#A0H326');
    }

    /**
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage Background color must be in hexadecimal format.
     */
    public function testSetBackgroundNoHash()
    {
        $options = new ImageOptions();
        $options->setBackgroundColor('A0F326');
    }

    public function testQueryBackgroundColor()
    {
        $options = new ImageOptions();
        $options->setResizeFit('pad');
        $options->setBackgroundColor('#a0f326');

        $this->assertSame('fit=pad&bg=rgb%3Aa0f326', $options->getQueryString());
    }

    public function testQueryCombined()
    {
        $options = new ImageOptions();
        $options
            ->setWidth(30)
            ->setHeight(40)
            ->setFormat('jpg')
            ->setProgressive(\true)
            ->setQuality(80)
        ;

        $this->assertSame('w=30&h=40&fm=jpg&q=80&fl=progressive', $options->getQueryString());
    }
}
