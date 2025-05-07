<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2025 Contentful GmbH
 * @license   MIT
 */

declare(strict_types=1);

namespace Contentful\Tests\Core\Implementation;

use Contentful\Core\ResourceBuilder\BaseResourceBuilder;
use Contentful\Core\ResourceBuilder\MapperInterface;

class ResourceBuilder extends BaseResourceBuilder
{
    protected function getMapperNamespace(): string
    {
        return __NAMESPACE__;
    }

    protected function createMapper($fqcn): MapperInterface
    {
        if ('Mapper' !== mb_substr($fqcn, -6)) {
            $fqcn .= 'Mapper';
        }

        return new $fqcn();
    }

    protected function getSystemType(array $data): string
    {
        return $data['sys']['type'];
    }
}
