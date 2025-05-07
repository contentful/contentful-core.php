<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2025 Contentful GmbH
 * @license   MIT
 */

declare(strict_types=1);

namespace Contentful\Tests\Core\Implementation;

use Contentful\Core\Api\Link;
use Contentful\Core\Resource\EntryInterface;

class Entry implements EntryInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $type;

    /**
     * @var array
     */
    private $parameters;

    public function __construct(string $id, string $type, array $parameters = [])
    {
        $this->id = $id;
        $this->type = $type;
        $this->parameters = $parameters;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getSystemProperties()
    {
        throw new \Exception('Not supported');
    }

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'parameters' => $this->parameters,
        ];
    }

    public function asLink(): Link
    {
        return new Link($this->id, $this->type);
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }
}
