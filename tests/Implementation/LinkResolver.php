<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2021 Contentful GmbH
 * @license   MIT
 */

declare(strict_types=1);

namespace Contentful\Tests\Core\Implementation;

use Contentful\Core\Api\Link;
use Contentful\Core\Api\LinkResolverInterface;
use Contentful\Core\Resource\ResourceInterface;

class LinkResolver implements LinkResolverInterface
{
    /**
     * {@inheritdoc}
     */
    public function resolveLink(Link $link, array $parameters = []): ResourceInterface
    {
        return new Entry($link->getId(), $link->getLinkType(), $parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function resolveLinkCollection(array $links, array $parameters = []): array
    {
        return \array_map(function (Link $link) use ($parameters): ResourceInterface {
            return $this->resolveLink($link, $parameters);
        }, $links);
    }
}
