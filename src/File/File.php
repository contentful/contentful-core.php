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
 * File class.
 */
class File implements ProcessedFileInterface
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
     * @param int $size Size in bytes
     */
    public function __construct(string $fileName, string $contentType, string $url, int $size)
    {
        $this->fileName = $fileName;
        $this->contentType = $contentType;
        $this->url = $url;
        $this->size = $size;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getContentType(): string
    {
        return $this->contentType;
    }

    public function getUrl(?UrlOptionsInterface $options = null): string
    {
        return $this->url;
    }

    /**
     * The size in bytes of this file.
     */
    public function getSize(): int
    {
        return $this->size;
    }

    public function jsonSerialize(): array
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
