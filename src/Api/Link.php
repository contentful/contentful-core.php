<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2024 Contentful GmbH
 * @license   MIT
 */

declare(strict_types=1);

namespace Contentful\Core\Api;

/**
 * Link class.
 *
 * A Link in Contentful represents a reference to any kind of resource.
 */
class Link implements \JsonSerializable
{
    /**
     * @var string
     */
    private $linkId;

    /**
     * @var string
     */
    private $linkType;

    /**
     * Link constructor.
     */
    public function __construct(string $linkId, string $linkType)
    {
        $this->linkId = $linkId;
        $this->linkType = $linkType;
    }

    /**
     * Get the ID of the referenced resource.
     */
    public function getId(): string
    {
        return $this->linkId;
    }

    /**
     * Get the type of the Link.
     */
    public function getLinkType(): string
    {
        return $this->linkType;
    }

    public function jsonSerialize(): array
    {
        return [
            'sys' => [
                'type' => 'Link',
                'id' => $this->linkId,
                'linkType' => $this->linkType,
            ],
        ];
    }
}
