<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2020 Contentful GmbH
 * @license   MIT
 */

declare(strict_types=1);

namespace Contentful\Tests\Core\Unit\Resource;

use Contentful\Tests\Core\Implementation\ResourceBuilder;
use Contentful\Tests\Core\Implementation\SecretMapper;
use Contentful\Tests\Core\Implementation\SecretResource;
use Contentful\Tests\TestCase;

class BaseResourceBuilderTest extends TestCase
{
    public function testBuild()
    {
        $builder = new ResourceBuilder();

        $resource = $builder->build([
            'sys' => [
                'id' => 'resourceId',
                'type' => 'Entry',
            ],
            'title' => 'My awesome entry',
        ]);

        $this->assertSame('resourceId', $resource->getId());
        $this->assertSame('Entry', $resource->getType());
        $this->assertSame('My awesome entry', $resource->getTitle());
    }

    public function testCustomMatcher()
    {
        $builder = new ResourceBuilder();
        $builder->setDataMapperMatcher('Entry', function (array $data) {
            if (isset($data['secretId'])) {
                return SecretMapper::class;
            }
        });

        $secretResource = $builder->build([
            'sys' => [
                'id' => 'resourceId',
                'type' => 'Entry',
            ],
            'secretId' => 'My super secret ID',
        ]);

        $this->assertInstanceOf(SecretResource::class, $secretResource);
        $this->assertSame('resourceId', $secretResource->getId());
        $this->assertSame('Entry', $secretResource->getType());
        $this->assertSame('My super secret ID', $secretResource->getSecretId());

        $resource = $builder->build([
            'sys' => [
                'id' => 'resourceId',
                'type' => 'Entry',
            ],
            'title' => 'My awesome entry',
        ]);

        $this->assertSame('resourceId', $resource->getId());
        $this->assertSame('Entry', $resource->getType());
        $this->assertSame('My awesome entry', $resource->getTitle());
    }

    public function testInvalidMatch()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Mapper class "MyInvalidMapper" does not exist.');

        $builder = new ResourceBuilder();
        $builder->setDataMapperMatcher('Entry', function (array $data) {
            return 'MyInvalidMapper';
        });

        $builder->build([
            'sys' => [
                'type' => 'Entry',
            ],
        ]);
    }
}
