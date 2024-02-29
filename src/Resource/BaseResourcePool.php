<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2024 Contentful GmbH
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
     */
    protected function sanitize(string $value): string
    {
        return strtr($value, [
            '.' => '___46___',
            '-' => '___45___',
            '_' => '___95___',
            '*' => '___42___',
        ]);
    }
}
