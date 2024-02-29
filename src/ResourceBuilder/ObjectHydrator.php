<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2024 Contentful GmbH
 * @license   MIT
 */

declare(strict_types=1);

namespace Contentful\Core\ResourceBuilder;

/**
 * Class ObjectHydrator.
 *
 * Utility class for handling updating private or protected properties of an object.
 */
class ObjectHydrator
{
    /**
     * @var \Closure[]
     */
    private $hydrators = [];

    /**
     * If given a class name as target, the hydrator will create an instance of that class,
     * but skipping the constructor. The hydrator will then update the internal properties,
     * according to the keys defined in the $data parameter.
     *
     * @param string|object $target
     *
     * @return object
     */
    public function hydrate($target, array $data)
    {
        $class = \is_object($target) ? $target::class : $target;
        if (\is_string($target)) {
            $target = (new \ReflectionClass($class))
                ->newInstanceWithoutConstructor()
            ;
        }

        $hydrator = $this->getHydrator($class);
        $hydrator($target, $data);

        return $target;
    }

    /**
     * @param string $class
     */
    private function getHydrator($class): \Closure
    {
        if (isset($this->hydrators[$class])) {
            return $this->hydrators[$class];
        }

        return $this->hydrators[$class] = \Closure::bind(function ($object, array $properties) {
            foreach ($properties as $property => $value) {
                $object->$property = $value;
            }
        }, null, $class);
    }
}
