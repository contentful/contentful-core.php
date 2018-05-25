<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2018 Contentful GmbH
 * @license   MIT
 */

namespace Contentful\Core\File;

/**
 * File class.
 */
class File implements FileInterface
{
    /**
     * @var string
     */
    private $fileName;

    /**
     * @var string
     */
    private $contentType;

    /**
     * @var string
     */
    private $url;

    /**
     * @var int
     */
    private $size;

    /**
     * File constructor.
     *
     * @param string $fileName
     * @param string $contentType
     * @param string $url
     * @param int    $size        Size in bytes
     */
    public function __construct($fileName, $contentType, $url, $size)
    {
        $this->fileName = $fileName;
        $this->contentType = $contentType;
        $this->url = $url;
        $this->size = $size;
    }

    /**
     * {@inheritdoc}
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * {@inheritdoc}
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * The URL where this file can be retrieved.
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * The size in bytes of this file.
     *
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'fileName' => $this->fileName,
            'contentType' => $this->contentType,
            'details' => [
                'size' => $this->size,
            ],
            'url' => $this->url,
        ];
    }
}
