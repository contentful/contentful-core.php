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
 * ResourcePoolInterface.
 *
 * Classes implementing this interface represent a pool which contains
 * ResourceInterface objects. This can be used for local storage and to simplify
 * dependency handling between resources.
 */
interface ResourcePoolInterface
{
    /**
     * Saves the given resource into the pool.
     *
     * @throws \RuntimeException If the saving process fails
     *
     * @return bool True is the resource was successfully added, false if it was already present
     */
    public function save(ResourceInterface $resource): bool;

    /**
     * Returns the resource for the given key.
     *
     * @throws \OutOfBoundsException If the given key does not represent any stored resource
     */
    public function get(string $type, string $id, array $options = []): ResourceInterface;

    /**
     * Returns whether the pool contains the given resource.
     */
    public function has(string $type, string $id, array $options = []): bool;

    /**
     * Generates a unique key for the given data.
     */
    public function generateKey(string $type, string $id, array $options = []): string;
}
