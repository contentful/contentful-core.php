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

    /**
     * RemoteUploadFile constructor.
     *
     * @param string $fileName
     * @param string $contentType
     * @param string $upload
     */
    public function __construct(string $fileName, string $contentType, string $upload)
    {
        $this->fileName = $fileName;
        $this->contentType = $contentType;
        $this->upload = $upload;
    }

    /**
     * {@inheritdoc}
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * {@inheritdoc}
     */
    public function getContentType(): string
    {
        return $this->contentType;
    }

    /**
     * @return string
     */
    public function getUpload(): string
    {
        return $this->upload;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize(): array
    {
        return [
            'fileName' => $this->fileName,
            'contentType' => $this->contentType,
            'upload' => $this->upload,
        ];
    }
}
