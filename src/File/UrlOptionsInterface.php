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
 * UrlOptionsInterface.
 */
interface UrlOptionsInterface
{
    /**
     * The urlencoded query string for these options.
     */
    public function getQueryString(): string;
}
