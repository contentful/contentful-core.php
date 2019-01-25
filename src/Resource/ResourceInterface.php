<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2019 Contentful GmbH
 * @license   MIT
 */

declare(strict_types=1);

namespace Contentful\Core\Resource;

use Contentful\Core\Api\Link;

/**
 * ResourceInterface.
 *
 * Represents a resource managed by Contentful.
 */
interface ResourceInterface extends \JsonSerializable
{
    /**
     * Returns the resource's system properties,
     * defined in the object "sys" in Contentful's responses.
     *
     * @return SystemPropertiesInterface
     */
    public function getSystemProperties();

    /**
     * Creates a Link representation of the current resource.
     *
     * @return Link
     */
    public function asLink(): Link;

    /**
     * Shortcut for retrieving the resource ID
     * from the system properties object.
     *
     * @return string
     */
    public function getId(): string;

    /**
     * Shortcut for retrieving the resource type
     * from the system properties object.
     *
     * @return string
     */
    public function getType(): string;
}
