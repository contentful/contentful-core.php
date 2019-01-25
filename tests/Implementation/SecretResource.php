<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2019 Contentful GmbH
 * @license   MIT
 */

declare(strict_types=1);

namespace Contentful\Tests\Core\Implementation;

use Contentful\Core\Api\Link;
use Contentful\Core\Resource\ResourceInterface;
use Contentful\Core\Resource\SystemPropertiesInterface;

class SecretResource implements ResourceInterface
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
     * @var string
     */
    private $secretId;

    /**
     * SecretResource constructor.
     *
     * @param string $id
     * @param string $type
     * @param string $secretId
     */
    public function __construct(string $id, string $type, string $secretId)
    {
        $this->id = $id;
        $this->type = $type;
        $this->secretId = $secretId;
    }

    /**
     * {@inheritdoc}
     */
    public function getSystemProperties(): SystemPropertiesInterface
    {
        return \null;
    }

    /**
     * {@inheritdoc}
     */
    public function asLink(): Link
    {
        return new Link($this->id, $this->type);
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function getSecretId(): string
    {
        return $this->secretId;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize(): array
    {
        return [];
    }
}
