<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2018 Contentful GmbH
 * @license   MIT
 */

namespace Contentful\Core\File;

/**
 * RemoteUploadFile class.
 */
class RemoteUploadFile implements UnprocessedFileInterface
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
    private $upload;

    public function __construct($fileName, $contentType, $upload)
    {
        $this->fileName = $fileName;
        $this->contentType = $contentType;
        $this->upload = $upload;
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
     * @return string
     */
    public function getUpload()
    {
        return $this->upload;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'fileName' => $this->fileName,
            'contentType' => $this->contentType,
            'upload' => $this->upload,
        ];
    }
}
