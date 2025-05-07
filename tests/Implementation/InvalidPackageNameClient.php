<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2025 Contentful GmbH
 * @license   MIT
 */

declare(strict_types=1);

namespace Contentful\Tests\Core\Implementation;

use Contentful\Core\Api\BaseClient;
use Contentful\Core\Resource\ResourceInterface;

class InvalidPackageNameClient extends BaseClient
{
    public function request(string $method, string $uri, array $options = []): ResourceInterface
    {
        // TODO: Implement request() method.
    }

    public function callApi(string $method, string $path, array $options = []): array
    {
        return parent::callApi($method, $path, $options);
    }

    public function getApi(): string
    {
        return 'DELIVERY';
    }

    protected static function getPackageName(): string
    {
        return 'invalid/invalid';
    }

    protected static function getSdkName(): string
    {
        return 'invalid';
    }

    protected static function getApiContentType(): string
    {
        return 'application/vnd.contentful.delivery.v1+json';
    }
}
