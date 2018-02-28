<?php

/**
 * This file is part of the contentful-core.php package.
 *
 * @copyright 2015-2018 Contentful GmbH
 * @license   MIT
 */

namespace Contentful\Tests\Core\Unit\Resource;

use Contentful\Core\Api\Link;
use Contentful\Core\Resource\ResourceInterface;
use Contentful\Core\ResourceBuilder\BaseResourceBuilder;
use Contentful\Core\ResourceBuilder\MapperInterface;
use Contentful\Tests\Core\TestCase;

class BaseResourceBuilderTest extends TestCase
{
    public function testBuild()
    {
        $builder = new ConcreteResourceBuilder();

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
        $builder = new ConcreteResourceBuilder();
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

    /**
     * @expectedException        \RuntimeException
     * @expectedExceptionMessage Mapper class "MyInvalidMapper" does not exist.
     */
    public function testInvalidMatch()
    {
        $builder = new ConcreteResourceBuilder();
        $builder->setDataMapperMatcher('Entry', function (array $data) {
            return 'MyInvalidMapper';
        });

        $secretResource = $builder->build([
            'sys' => [
                'type' => 'Entry',
            ],
        ]);
    }
}

class ConcreteResourceBuilder extends BaseResourceBuilder
{
    protected function getMapperNamespace()
    {
        return __NAMESPACE__;
    }

    protected function createMapper($fqcn)
    {
        if ('Mapper' !== \mb_substr($fqcn, -6)) {
            $fqcn .= 'Mapper';
        }

        return new $fqcn();
    }

    protected function getSystemType(array $data)
    {
        return $data['sys']['type'];
    }
}

class EntryMapper implements MapperInterface
{
    public function map($resource, array $data)
    {
        return new ConcreteResource($data['sys']['id'], $data['sys']['type'], $data['title']);
    }
}

class SecretMapper implements MapperInterface
{
    public function map($resource, array $data)
    {
        return new SecretResource($data['sys']['id'], $data['sys']['type'], $data['secretId']);
    }
}

class ConcreteResource implements ResourceInterface
{
    private $id;

    private $type;

    private $title;

    public function __construct($id, $type, $title)
    {
        $this->id = $id;
        $this->type = $type;
        $this->title = $title;
    }

    public function getSystemProperties()
    {
        return null;
    }

    public function asLink()
    {
        return new Link($this->id, $this->type);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function jsonSerialize()
    {
        return [];
    }
}

class SecretResource implements ResourceInterface
{
    private $id;

    private $type;

    private $secretId;

    public function __construct($id, $type, $secretId)
    {
        $this->id = $id;
        $this->type = $type;
        $this->secretId = $secretId;
    }

    public function getSystemProperties()
    {
        return null;
    }

    public function asLink()
    {
        return new Link($this->id, $this->type);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getSecretId()
    {
        return $this->secretId;
    }

    public function jsonSerialize()
    {
        return [];
    }
}
