<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2024 Contentful GmbH
 * @license   MIT
 */

declare(strict_types=1);

namespace Contentful\Core\ResourceBuilder;

use Contentful\Core\Resource\ResourceArray;
use Contentful\Core\Resource\ResourceInterface;

/**
 * MapperInterface.
 *
 * This interface must be implemented by those classes that are capable
 * of transforming a raw array of data fetched from the API to an actual
 * PHP object.
 */
interface MapperInterface
{
    /**
     * Maps the given data to a resource.
     *
     * ATTENTION: This will directly modify the given resource.
     * If $resource is "null", the method is expected to create
     * a new instance of the appropriate class.
     *
     * @param ResourceInterface|null $resource
     *
     * @return ResourceInterface|ResourceArray
     */
    public function map($resource, array $data);
}
