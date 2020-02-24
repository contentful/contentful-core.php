<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2020 Contentful GmbH
 * @license   MIT
 */

declare(strict_types=1);

namespace Contentful\Tests\Core\Unit\File;

use Contentful\Core\File\ImageOptions;
use Contentful\Tests\TestCase;

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
    
    public function testSetWidthNegative()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Width must not be negative.");
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
    
    public function testSetHeightNegative()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Height must not be negative.");
        
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
    
    public function testSetFormatInvalid()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Unknown format \"invalid\" given. Expected \"png, jpg, webp\" or null.");
        
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
    
    public function testSetQualityNegative()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Quality must be between 1 and 100, \"-50\" given.");
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

    public function testQueryPng8Bit()
    {
        $options = new ImageOptions();
        $options->setPng8Bit(\true);

        $this->assertSame('fm=png&fl=png8', $options->getQueryString());
    }

    public function testQueryPng8BitOverridesFormat()
    {
        $options = (new ImageOptions())
            ->setFormat('jpg')
            ->setPng8Bit(\true)
        ;

        $this->assertSame('fm=png&fl=png8', $options->getQueryString());
    }
    
    public function testSetResizeFitInvalid()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Unknown resize fit \"invalid\" given. Expected \"pad, crop, fill, thumb, scale\" or null.");
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
    
    public function testSetResizeFocusInvalid()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Unknown resize focus \"invalid\" given. Expected \"face, faces, top, bottom, right, left, top_right, top_left, bottom_right, bottom_left, center\" or null.");
        
        
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
    
    public function testSetRadiusNegative()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Radius must not be negative.");
        
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
    
    public function testSetBackgroundColorTooShort()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Background color must be in hexadecimal format.");
        
        $options = new ImageOptions();
        $options->setBackgroundColor('#A0F36');
    }
    
    public function testSetBackgroundInvalidCharacter()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Background color must be in hexadecimal format.");
        
        $options = new ImageOptions();
        $options->setBackgroundColor('#A0H326');
    }

    
    public function testSetBackgroundNoHash()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Background color must be in hexadecimal format.");
        
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
