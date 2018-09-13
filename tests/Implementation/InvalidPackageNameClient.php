<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2018 Contentful GmbH
 * @license   MIT
 */

declare(strict_types=1);

namespace Contentful\Tests\Core\Implementation;

use Contentful\Core\Api\BaseClient;

class InvalidPackageNameClient extends BaseClient
{
    /**
     * {@inheritdoc}
     */
    public function request(string $method, string $path, array $options = [])
    {
        return parent::request($method, $path, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function getApi(): string
    {
        return 'DELIVERY';
    }

    /**
     * {@inheritdoc}
     */
    protected function getPackageName(): string
    {
        return 'invalid/invalid';
    }

    /**
     * {@inheritdoc}
     */
    protected function getSdkName(): string
    {
        return 'invalid';
    }

    /**
     * {@inheritdoc}
     */
    protected function getApiContentType(): string
    {
        return 'application/vnd.contentful.delivery.v1+json';
    }
}
