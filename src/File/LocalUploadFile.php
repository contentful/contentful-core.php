<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2019 Contentful GmbH
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

    public function __construct(string $fileName, string $contentType, Link $uploadFrom)
    {
        $this->fileName = $fileName;
        $this->contentType = $contentType;
        $this->uploadFrom = $uploadFrom;
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
     * @return Link
     */
    public function getUploadFrom(): Link
    {
        return $this->uploadFrom;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize(): array
    {
        return [
            'fileName' => $this->fileName,
            'contentType' => $this->contentType,
            'uploadFrom' => $this->uploadFrom,
        ];
    }
}
