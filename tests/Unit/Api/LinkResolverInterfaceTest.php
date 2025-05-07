<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2025 Contentful GmbH
 * @license   MIT
 */

declare(strict_types=1);

namespace Contentful\Tests\Core\Unit\Api;

use Contentful\Core\Api\Link;
use Contentful\Tests\Core\Implementation\Entry;
use Contentful\Tests\Core\Implementation\LinkResolver;
use Contentful\Tests\TestCase;

class LinkResolverInterfaceTest extends TestCase
{
    public function testLinkResolver()
    {
        $linkResolver = new LinkResolver();

        /** @var Entry $entry */
        $entry = $linkResolver->resolveLink(
            new Link('someEntryId', 'Entry'),
            ['someParameter' => 'someValue']
        );

        $this->assertSame('someEntryId', $entry->getId());
        $this->assertSame('Entry', $entry->getType());
        $this->assertSame(['someParameter' => 'someValue'], $entry->getParameters());

        /** @var Entry[] $entries */
        $entries = $linkResolver->resolveLinkCollection(
            [new Link('someEntryId', 'Entry')],
            ['someParameter' => 'someValue']
        );

        $this->assertContainsOnlyInstancesOf(Entry::class, $entries);
        $this->assertCount(1, $entries);

        $entry = $entries[0];

        $this->assertSame('someEntryId', $entry->getId());
        $this->assertSame('Entry', $entry->getType());
        $this->assertSame(['someParameter' => 'someValue'], $entry->getParameters());
    }
}
