<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2020 Contentful GmbH
 * @license   MIT
 */

declare(strict_types=1);

namespace Contentful\Tests\Core\Implementation;

use Contentful\Core\Api\IntegrationInterface;

class Integration implements IntegrationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getIntegrationName(): string
    {
        return 'contentful.symfony';
    }

    /**
     * {@inheritdoc}
     */
    public function getIntegrationPackageName(): string
    {
        return 'contentful/contentful-bundle';
    }
}
