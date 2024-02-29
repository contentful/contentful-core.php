<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2024 Contentful GmbH
 * @license   MIT
 */

declare(strict_types=1);

namespace Contentful\Core\Api;

interface IntegrationInterface
{
    /**
     * Returns the name of the current integration.
     * This value must be the one that is sent as part of
     * the "X-Contentful-User-Agent" header to the API.
     */
    public function getIntegrationName(): string;

    /**
     * Returns the package name of the current integration.
     * This value must be the one defined in the "composer.json" file.
     */
    public function getIntegrationPackageName(): string;
}
