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
 * FileInterface.
 */
interface FileInterface extends \JsonSerializable
{
    /**
     * The name of this file.
     *
     * @return string
     */
    public function getFileName(): string;

    /**
     * The Content- (or MIME-)Type of this file.
     *
     * @return string
     */
    public function getContentType(): string;
}
