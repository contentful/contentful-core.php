<?php

/**
 * This file is part of the contentful-core.php package.
 *
 * @copyright 2015-2018 Contentful GmbH
 * @license   MIT
 */

namespace Contentful\Core\File;

use Contentful\Core\Api\Link;

/**
 * LocalUploadFile class.
 */
class LocalUploadFile implements UnprocessedFileInterface
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
     * @var Link
     */
    private $uploadFrom;

    public function __construct($fileName, $contentType, Link $uploadFrom)
    {
        $this->fileName = $fileName;
        $this->contentType = $contentType;
        $this->uploadFrom = $uploadFrom;
    }

    /**
     * The name of this file.
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * The Content- (or MIME-)Type of this file.
     *
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @return Link
     */
    public function getUploadFrom()
    {
        return $this->uploadFrom;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'fileName' => $this->fileName,
            'contentType' => $this->contentType,
            'uploadFrom' => $this->uploadFrom,
        ];
    }
}
