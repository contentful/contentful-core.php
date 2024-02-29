<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2024 Contentful GmbH
 * @license   MIT
 */

declare(strict_types=1);

namespace Contentful\Core\Resource;

/**
 * SystemPropertiesInterface.
 *
 * This interface represents an object of Contentful-managed system properties.
 */
interface SystemPropertiesInterface extends \JsonSerializable
{
    /**
     * @param array $data An array corresponding to the `sys` property in a Contentful resource
     */
    public function __construct(array $data);

    public function getId(): string;

    public function getType(): string;
}
