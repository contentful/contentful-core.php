<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2021 Contentful GmbH
 * @license   MIT
 */

declare(strict_types=1);

namespace Contentful\Core\Resource;

class ArraySystemProperties implements SystemPropertiesInterface
{
    /**
     * Data supplied to the constructor. We need to save this shortly to avoid
     * code warnings.
     */
    private $data;

    /**
     * {@inheritdoc}
     */
    public function __construct(array $data)
    {
        // this avoids the unused warning
        $this->data = $data;
        // this avoids using more RAM than necassary :)
        $this->data = null;
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): string
    {
        throw new \LogicException('Resource of type Array does not have an ID.');
    }

    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return 'Array';
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize(): array
    {
        return [
            'type' => 'Array',
        ];
    }
}
