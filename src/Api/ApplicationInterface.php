<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2024 Contentful GmbH
 * @license   MIT
 */

declare(strict_types=1);

namespace Contentful\Core\Api;

interface ApplicationInterface
{
    /**
     * Returns the name of the current application.
     * This value must be the one that is sent as part of
     * the "X-Contentful-User-Agent" header to the API.
     */
    public function getApplicationName(): string;

    /**
     * Returns whether the current application is distributed as a Composer package.
     */
    public function isPackagedApplication(): bool;

    /**
     * Returns the package name of the current application.
     * If the application is not distributed as a package, this method
     * must return an empty string.
     */
    public function getApplicationPackageName(): string;

    /**
     * Returns the version of the current application.
     * This must return an actual version if the application is not distributed
     * as a Composer package.
     */
    public function getApplicationVersion(): string;
}
