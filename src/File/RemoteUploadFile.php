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
 * RemoteUploadFile class.
 */
class RemoteUploadFile implements UnprocessedFileInterface
{
    /**
     * @var string|null
     */
    private $fileName;

    /**
     * @var string|null
     */
    private $contentType;

    /**
     * @var string
     */
    private $upload;

    /**
     * RemoteUploadFile constructor.
     */
    public function __construct(?string $fileName, ?string $contentType, string $upload)
    {
        $this->fileName = $fileName;
        $this->contentType = $contentType;
        $this->upload = $upload;
    }

    public function getFileName(): string
    {
        return $this->fileName ?? '';
    }

    public function getContentType(): string
    {
        return $this->contentType ?? '';
    }

    public function getUpload(): string
    {
        return $this->upload;
    }

    public function jsonSerialize(): array
    {
        return [
            'fileName' => $this->fileName,
            'contentType' => $this->contentType,
            'upload' => $this->upload,
        ];
    }
}
