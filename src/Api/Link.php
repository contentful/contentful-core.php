<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2018 Contentful GmbH
 * @license   MIT
 */

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
     *
     * @param string $linkId
     * @param string $linkType
     */
    public function __construct($linkId, $linkType)
    {
        $this->linkId = $linkId;
        $this->linkType = $linkType;
    }

    /**
     * Get the ID of the referenced resource.
     *
     * @return string
     */
    public function getId()
    {
        return $this->linkId;
    }

    /**
     * Get the type of the Link.
     *
     * @return string
     */
    public function getLinkType()
    {
        return $this->linkType;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
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
