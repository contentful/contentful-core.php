<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2019 Contentful GmbH
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

    /**
     * @return string
     */
    public function getId(): string;

    /**
     * @return string
     */
    public function getType(): string;
}
