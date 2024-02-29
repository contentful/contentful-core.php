<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2024 Contentful GmbH
 * @license   MIT
 */

declare(strict_types=1);

namespace Contentful\Core\Resource;

use Contentful\Core\Api\Link;

/**
 * A ResourceArray holds the response of an API request
 * if more than one resource has been requested.
 *
 * In addition to the retrieved items themselves
 * it also provides some access to metadata.
 *
 * @implements \IteratorAggregate<int, \Contentful\Core\Resource\ResourceInterface>
 */
class ResourceArray implements ResourceInterface, \Countable, \ArrayAccess, \IteratorAggregate
{
    /**
     * @var array
     */
    private $items;

    /**
     * @var int
     */
    private $total;

    /**
     * @var int
     */
    private $limit;

    /**
     * @var int
     */
    private $skip;

    /**
     * ResourceArray constructor.
     */
    public function __construct(array $items, int $total, int $limit, int $skip)
    {
        $this->items = $items;
        $this->total = $total;
        $this->limit = $limit;
        $this->skip = $skip;
    }

    /**
     * Returns the total amount of resources matching the filter.
     */
    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * The limit used when retrieving this ResourceArray.
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * The number of skipped resources when retrieving this  ResourceArray.
     */
    public function getSkip(): int
    {
        return $this->skip;
    }

    /**
     * Get the returned values as a PHP array.
     *
     * @return ResourceInterface[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function getSystemProperties(): SystemPropertiesInterface
    {
        return new ArraySystemProperties([]);
    }

    public function asLink(): Link
    {
        throw new \LogicException('Resource of type Array can not be represented as a Link object.');
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
            'sys' => [
                'type' => 'Array',
            ],
            'total' => $this->total,
            'limit' => $this->limit,
            'skip' => $this->skip,
            'items' => $this->items,
        ];
    }

    public function count(): int
    {
        return \count($this->items);
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->items);
    }

    public function offsetExists($offset): bool
    {
        return isset($this->items[$offset]);
    }

    public function offsetGet($offset): ResourceInterface
    {
        return $this->items[$offset];
    }

    public function offsetSet($offset, $value): void
    {
        throw new \BadMethodCallException(sprintf('"%s" is read-only.', __CLASS__));
    }

    public function offsetUnset($offset): void
    {
        throw new \BadMethodCallException(sprintf('"%s" is read-only.', __CLASS__));
    }
}
