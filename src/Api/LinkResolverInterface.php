<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2024 Contentful GmbH
 * @license   MIT
 */

declare(strict_types=1);

namespace Contentful\Core\Api;

use Contentful\Core\Resource\ResourceInterface;

interface LinkResolverInterface
{
    /**
     * @param string[] $parameters
     */
    public function resolveLink(Link $link, array $parameters = []): ResourceInterface;

    /**
     * Resolves an array of links.
     * A method implementing this may apply some optimizations
     * to reduce the amount of necessary API calls, or simply forward this
     * to the "resolveLink" method.
     *
     * @param Link[]   $links
     * @param string[] $parameters
     *
     * @return ResourceInterface[]
     */
    public function resolveLinkCollection(array $links, array $parameters = []): array;
}
