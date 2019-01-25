<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2019 Contentful GmbH
 * @license   MIT
 */

declare(strict_types=1);

namespace Contentful\Core\File;

/**
 * ImageFile class.
 */
class ImageFile extends File
{
    /**
     * @var int
     */
    private $width;

    /**
     * @var int
     */
    private $height;

    /**
     * ImageFile constructor.
     *
     * @param string $fileName
     * @param string $contentType
     * @param string $url
     * @param int    $size
     * @param int    $width
     * @param int    $height
     */
    public function __construct(string $fileName, string $contentType, string $url, int $size, int $width, int $height)
    {
        parent::__construct($fileName, $contentType, $url, $size);

        $this->width = $width;
        $this->height = $height;
    }

    /**
     * Returns the width of the image.
     *
     * @return int
     */
    public function getWidth(): int
    {
        return $this->width;
    }

    /**
     * Returns the height of the image.
     *
     * @return int
     */
    public function getHeight(): int
    {
        return $this->height;
    }

    /**
     * @param UrlOptionsInterface|null $options
     *
     * @return string
     */
    public function getUrl(UrlOptionsInterface $options = \null): string
    {
        $query = \null !== $options ? '?'.$options->getQueryString() : '';

        return parent::getUrl().$query;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize(): array
    {
        $file = parent::jsonSerialize();
        $file['details']['image'] = [
            'width' => $this->width,
            'height' => $this->height,
        ];

        return $file;
    }
}
