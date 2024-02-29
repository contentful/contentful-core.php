<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2024 Contentful GmbH
 * @license   MIT
 */

declare(strict_types=1);

namespace Contentful\Core\Resource;

class ArraySystemProperties implements SystemPropertiesInterface
{
    public function __construct(array $data)  // @phpstan-ignore-line
    {
        // We need to have PHPStan ignore the constructor line, as the data parameter is needed for inherited types, but
        // not in this base class.
    }

    public function getId(): string
    {
        throw new \LogicException('Resource of type Array does not have an ID.');
    }

    public function getType(): string
    {
        return 'Array';
    }

    public function jsonSerialize(): array
    {
        return [
            'type' => 'Array',
        ];
    }
}
