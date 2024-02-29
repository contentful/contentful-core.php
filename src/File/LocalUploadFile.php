<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2024 Contentful GmbH
 * @license   MIT
 */

declare(strict_types=1);

namespace Contentful\Core\File;

use Contentful\Core\Api\Link;

/**
 * LocalUploadFile class.
 */
class LocalUploadFile implements UnprocessedFileInterface
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
     * @var Link
     */
    private $uploadFrom;

    public function __construct(?string $fileName, ?string $contentType, Link $uploadFrom)
    {
        $this->fileName = $fileName;
        $this->contentType = $contentType;
        $this->uploadFrom = $uploadFrom;
    }

    public function getFileName(): string
    {
        return $this->fileName ?? '';
    }

    public function getContentType(): string
    {
        return $this->contentType ?? '';
    }

    public function getUploadFrom(): Link
    {
        return $this->uploadFrom;
    }

    public function jsonSerialize(): array
    {
        return [
            'fileName' => $this->fileName,
            'contentType' => $this->contentType,
            'uploadFrom' => $this->uploadFrom,
        ];
    }
}
