<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2018 Contentful GmbH
 * @license   MIT
 */

declare(strict_types=1);

namespace Contentful\Core\Resource;

abstract class BaseResourcePool implements ResourcePoolInterface
{
    /**
     * @var array<string, ResourceInterface>
     */
    protected $resources = [];

    /**
     * Sanitizes potentially problematic characters for resource keys.
     *
     * @param string $value
     *
     * @return string
     */
    protected function sanitize(string $value): string
    {
        return \preg_replace_callback('/[\.\-\_\*]/', function (array $matches): string {
            return '___'.\ord($matches[0]).'___';
        }, $value);
    }
}
