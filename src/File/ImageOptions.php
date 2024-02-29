<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2024 Contentful GmbH
 * @license   MIT
 */

declare(strict_types=1);

namespace Contentful\Core\File;

/**
 * ImageOptions class.
 *
 * This class allows specifying extended options to the Contentful Image API,
 * to resize images or change their format.
 *
 * @see https://www.contentful.com/developers/docs/references/images-api/#/reference Image API Reference
 */
class ImageOptions implements UrlOptionsInterface
{
    /**
     * @var int|null
     */
    private $width;

    /**
     * @var int|null
     */
    private $height;

    /**
     * @var string|null
     */
    private $format;

    /**
     * @var int|null
     */
    private $quality;

    /**
     * @var bool
     */
    private $progressive = false;

    /**
     * @var bool
     */
    private $png8bit = false;

    /**
     * @var string|null
     */
    private $resizeFit;

    /**
     * @var string|null
     */
    private $resizeFocus;

    /**
     * @var float|null
     */
    private $radius;

    /**
     * @var string|null
     */
    private $backgroundColor;

    public function getQueryString(): string
    {
        $options = [
            'w' => $this->width,
            'h' => $this->height,
            'fm' => $this->format,
            'q' => $this->quality,
            'r' => $this->radius,
        ];

        if ($this->progressive) {
            $options['fm'] = 'jpg';
            $options['fl'] = 'progressive';
        }

        if ($this->png8bit) {
            $options['fm'] = 'png';
            $options['fl'] = 'png8';
        }

        if (null !== $this->resizeFit) {
            $options['fit'] = $this->resizeFit;

            if ((
                'pad' === $this->resizeFit
                || 'fill' === $this->resizeFit
                || 'crop' === $this->resizeFit
                || 'thumb' === $this->resizeFit
            )
            && null !== $this->resizeFocus) {
                $options['f'] = $this->resizeFocus;
            }
            if ('pad' === $this->resizeFit && null !== $this->backgroundColor) {
                $options['bg'] = 'rgb:'.mb_substr($this->backgroundColor, 1);
            }
        }

        return http_build_query($options, '', '&', \PHP_QUERY_RFC3986);
    }

    /**
     * Set the width of the image.
     *
     * The image will by default not be stretched, skewed or enlarged.
     * Instead it will be fit into the bounding box given by the width
     * and height parameters.
     *
     * Can be set to null to not set a width.
     *
     * @param int|null $width the width in pixel
     *
     * @throws \InvalidArgumentException If $width is negative
     *
     * @return $this
     */
    public function setWidth(?int $width = null)
    {
        if (null !== $width && $width < 0) {
            throw new \InvalidArgumentException('Width must not be negative.');
        }

        $this->width = $width;

        return $this;
    }

    /**
     * Set the height of the image.
     *
     * The image will by default not be stretched, skewed or enlarged.
     * Instead it will be fit into the bounding box given by the width
     * and height parameters.
     *
     * Can be set to null to not set a height.
     *
     * @param int|null $height the height in pixel
     *
     * @throws \InvalidArgumentException If $height is negative
     *
     * @return $this
     */
    public function setHeight(?int $height = null)
    {
        if (null !== $height && $height < 0) {
            throw new \InvalidArgumentException('Height must not be negative.');
        }

        $this->height = $height;

        return $this;
    }

    /**
     * Set the format of the image. Valid values are "png" and "jpg".
     * Can be set to null to not enforce a format.
     *
     * @throws \InvalidArgumentException If $format is not a valid value
     *
     * @return $this
     */
    public function setFormat(?string $format = null)
    {
        $validValues = ['png', 'jpg', 'webp'];

        if (null !== $format && !\in_array($format, $validValues, true)) {
            throw new \InvalidArgumentException(sprintf('Unknown format "%s" given. Expected "%s" or null.', $format, implode(', ', $validValues)));
        }

        $this->format = $format;

        return $this;
    }

    /**
     * Quality of the JPEG encoded image.
     * The image format will be forced to JPEG.
     *
     * @param int|null $quality if an int, between 1 and 100
     *
     * @throws \InvalidArgumentException If $quality is out of range
     *
     * @return $this
     */
    public function setQuality(?int $quality = null)
    {
        if (null !== $quality && ($quality < 1 || $quality > 100)) {
            throw new \InvalidArgumentException(sprintf('Quality must be between 1 and 100, "%d" given.', $quality));
        }

        $this->quality = $quality;

        return $this;
    }

    /**
     * Set to true to load the image as a progressive JPEG.
     * The image format will be forced to JPEG.
     *
     * @return $this
     */
    public function setProgressive(bool $progressive)
    {
        $this->progressive = $progressive;

        return $this;
    }

    /**
     * Set to true to load the image as a 8-bit PNG.
     * The image format will be forced to PNG.
     *
     * @return $this
     */
    public function setPng8Bit(bool $png8Bit)
    {
        $this->png8bit = $png8Bit;

        return $this;
    }

    /**
     * Change the behavior when resizing the image.
     *
     * By default, images are resized to fit inside the bounding box
     * set trough setWidth and setHeight while retaining their aspect ratio.
     *
     * Possible values are:
     * - null for the default value
     * - 'pad' Same as the default, but add padding so that the generated image has exactly the given dimensions.
     * - 'crop' Crop a part of the original image.
     * - 'fill' Fill the given dimensions by cropping the image.
     * - 'thumb' Create a thumbnail of detected faces from image, used with 'setFocus'.
     * - 'scale' Scale the image regardless of the original aspect ratio.
     *
     * @throws \InvalidArgumentException For unknown values of $resizeBehavior
     *
     * @return $this
     */
    public function setResizeFit(?string $resizeFit = null)
    {
        $validValues = ['pad', 'crop', 'fill', 'thumb', 'scale'];

        if (null !== $resizeFit && !\in_array($resizeFit, $validValues, true)) {
            throw new \InvalidArgumentException(sprintf('Unknown resize fit "%s" given. Expected "%s" or null.', $resizeFit, implode(', ', $validValues)));
        }

        $this->resizeFit = $resizeFit;

        return $this;
    }

    /**
     * Set the focus area when the resize fit is set to 'thumb'.
     *
     * Possible values are:
     * - 'top', 'right', 'left', 'bottom'
     * - A combination like 'bottom_right'
     * - 'face' or 'faces' to focus the resizing via face detection
     *
     * @throws \InvalidArgumentException For unknown values of $resizeFocus
     *
     * @return $this
     */
    public function setResizeFocus(?string $resizeFocus = null)
    {
        $validValues = [
            'face',
            'faces',
            'top',
            'bottom',
            'right',
            'left',
            'top_right',
            'top_left',
            'bottom_right',
            'bottom_left',
            'center',
        ];

        if (null !== $resizeFocus && !\in_array($resizeFocus, $validValues, true)) {
            throw new \InvalidArgumentException(sprintf('Unknown resize focus "%s" given. Expected "%s" or null.', $resizeFocus, implode(', ', $validValues)));
        }

        $this->resizeFocus = $resizeFocus;

        return $this;
    }

    /**
     * Add rounded corners to your image or crop to a circle/ellipsis.
     *
     * @param float|null $radius a float value defining the corner radius
     *
     * @throws \InvalidArgumentException If $radius is negative
     *
     * @return $this
     */
    public function setRadius(?float $radius = null)
    {
        if (null !== $radius && $radius < 0) {
            throw new \InvalidArgumentException('Radius must not be negative.');
        }

        $this->radius = $radius;

        return $this;
    }

    /**
     * Background color, relevant if the resize fit type 'pad' is used.
     * Expects a valid hexadecimal HTML color like '#9090ff'. Default is transparency.
     *
     * @throws \InvalidArgumentException if the $backgroundColor is not in hexadecimal format
     *
     * @return $this
     */
    public function setBackgroundColor(?string $backgroundColor = null)
    {
        if (null !== $backgroundColor && !preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $backgroundColor)) {
            throw new \InvalidArgumentException('Background color must be in hexadecimal format.');
        }

        $this->backgroundColor = $backgroundColor;

        return $this;
    }
}
