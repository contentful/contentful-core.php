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
 * ProcessedFileInterface.
 */
interface ProcessedFileInterface extends FileInterface
{
    /**
     * The URL where this file can be retrieved.
     *
     * @param UrlOptionsInterface|null $options
     *
     * @return string
     */
    public function getUrl(UrlOptionsInterface $options = \null);
}
