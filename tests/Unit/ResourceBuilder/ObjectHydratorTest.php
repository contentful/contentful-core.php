<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2025 Contentful GmbH
 * @license   MIT
 */

declare(strict_types=1);

namespace Contentful\Tests\Core\Unit\ResourceBuilder;

use Contentful\Core\ResourceBuilder\ObjectHydrator;
use Contentful\Tests\Core\Implementation\Person;
use Contentful\Tests\Core\Implementation\Videogame;
use Contentful\Tests\TestCase;

class ObjectHydratorTest extends TestCase
{
    public function testHydration()
    {
        $hydrator = new ObjectHydrator();

        /** @var Person $person */
        $person = $hydrator->hydrate(Person::class, [
            'name' => 'Kanji Tatsumi',
            'age' => 15,
        ]);

        $this->assertSame('Kanji Tatsumi', $person->getName());
        $this->assertSame(15, $person->getAge());

        /** @var Person $person */
        $person = $hydrator->hydrate(Person::class, [
            'name' => 'Makoto Niijima',
            'age' => 17,
        ]);

        $this->assertSame('Makoto Niijima', $person->getName());
        $this->assertSame(17, $person->getAge());

        $videogame = new Videogame();
        $this->assertSame('', $videogame->getTitle());
        $this->assertSame('', $videogame->getConsole());

        $hydrator->hydrate($videogame, [
            'title' => 'Persona 5',
            'console' => 'PS4',
        ]);

        $this->assertSame('Persona 5', $videogame->getTitle());
        $this->assertSame('PS4', $videogame->getConsole());

        $reflectionObject = new \ReflectionObject($hydrator);
        $property = $reflectionObject->getProperty('hydrators');
        $property->setAccessible(true);
        $hydrators = $property->getValue($hydrator);

        $this->assertCount(2, $hydrators);
        $this->assertArrayHasKey(Person::class, $hydrators);
        $this->assertArrayHasKey(Videogame::class, $hydrators);

        $this->assertInstanceOf(\Closure::class, $hydrators[Person::class]);
        $this->assertInstanceOf(\Closure::class, $hydrators[Videogame::class]);
    }
}
