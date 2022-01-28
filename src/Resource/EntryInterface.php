<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2022 Contentful GmbH
 * @license   MIT
 */

declare(strict_types=1);

namespace Contentful\Core\Resource;

interface EntryInterface extends ResourceInterface
{
    public function has(string $name, string $locale = null, bool $checkLinksAreResolved = true): bool;

    public function all(string $locale = null, bool $resolveLinks = true, bool $ignoreLocaleForNonLocalizedFields = false): array;

    public function isFieldLocalized(string $name): bool;

    public function get(string $name, string $locale = null, bool $resolveLinks = true);

    public function initTags(array $tags);

    public function getTags(): array;
}
