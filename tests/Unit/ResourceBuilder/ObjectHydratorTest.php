<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2018 Contentful GmbH
 * @license   MIT
 */

namespace Contentful\Tests\Core\Unit\ResourceBuilder;

use Contentful\Core\ResourceBuilder\ObjectHydrator;
use Contentful\Tests\Core\TestCase;

class ObjectHydratorTest extends TestCase
{
    public function testHydration()
    {
        $hydrator = new ObjectHydrator();

        /** @var TestPerson $person */
        $person = $hydrator->hydrate(TestPerson::class, [
            'name' => 'Kanji Tatsumi',
            'age' => 15,
        ]);

        $this->assertSame('Kanji Tatsumi', $person->getName());
        $this->assertSame(15, $person->getAge());

        /** @var TestPerson $person */
        $person = $hydrator->hydrate(TestPerson::class, [
            'name' => 'Makoto Niijima',
            'age' => 17,
        ]);

        $this->assertSame('Makoto Niijima', $person->getName());
        $this->assertSame(17, $person->getAge());

        $videogame = new TestVideogame();
        $this->assertNull($videogame->getTitle());
        $this->assertNull($videogame->getConsole());

        $hydrator->hydrate($videogame, [
            'title' => 'Persona 5',
            'console' => 'PS4',
        ]);

        $this->assertSame('Persona 5', $videogame->getTitle());
        $this->assertSame('PS4', $videogame->getConsole());

        $reflectionObject = new \ReflectionObject($hydrator);
        $property = $reflectionObject->getProperty('hydrators');
        $property->setAccessible(\true);
        $hydrators = $property->getValue($hydrator);

        $this->assertCount(2, $hydrators);
        $this->assertArrayHasKey(TestPerson::class, $hydrators);
        $this->assertArrayHasKey(TestVideogame::class, $hydrators);

        $this->assertInstanceOf(\Closure::class, $hydrators[TestPerson::class]);
        $this->assertInstanceOf(\Closure::class, $hydrators[TestVideogame::class]);
    }
}

class TestPerson
{
    private $name;

    private $age;

    private function __construct()
    {
    }

    public function getName()
    {
        return $this->name;
    }

    public function getAge()
    {
        return $this->age;
    }
}

class TestVideogame
{
    private $title;

    private $console;

    public function getTitle()
    {
        return $this->title;
    }

    public function getConsole()
    {
        return $this->console;
    }
}
