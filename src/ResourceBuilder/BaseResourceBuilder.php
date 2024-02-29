<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2024 Contentful GmbH
 * @license   MIT
 */

declare(strict_types=1);

namespace Contentful\Core\ResourceBuilder;

use Contentful\Core\Resource\ResourceInterface;

/**
 * BaseResourceBuilder class.
 *
 * This class is responsible for populating PHP objects
 * using data received from Contentful's API.
 */
abstract class BaseResourceBuilder implements ResourceBuilderInterface
{
    /**
     * An array for caching mapper instances.
     *
     * @var MapperInterface[]
     */
    private $mappers = [];

    /**
     * An array for storing data matcher callables.
     *
     * @var (callable|null)[]
     */
    private $dataMapperMatchers = [];

    /**
     * The namespace where this resource builder will look for mappers.
     *
     * @var string
     */
    private $mapperNamespace;

    /**
     * ResourceBuilder constructor.
     */
    public function __construct()
    {
        $this->mapperNamespace = $this->getMapperNamespace();
    }

    public function build(array $data, ?ResourceInterface $resource = null)
    {
        $fqcn = $this->determineMapperFqcn($data);

        return $this->getMapper($fqcn)
            ->map($resource, $data)
        ;
    }

    /**
     * Returns the mapper object appropriate for the given data.
     *
     * @param string $fqcn
     *
     * @throws \RuntimeException
     *
     * @return MapperInterface
     */
    public function getMapper($fqcn)
    {
        if (!isset($this->mappers[$fqcn])) {
            $this->mappers[$fqcn] = $this->createMapper($fqcn);
        }

        return $this->mappers[$fqcn];
    }

    /**
     * Determines the fully-qualified class name of the mapper object
     * that will handle the mapping process.
     *
     * This function will use user-defined data matchers, if available.
     *
     * If the user-defined matcher does not return anything,
     * we default to the base mapper, so the user doesn't have
     * to manually return the default value.
     *
     * @param array $data The raw API data
     *
     * @return string The mapper's fully-qualified class name
     */
    private function determineMapperFqcn(array $data)
    {
        $type = $this->getSystemType($data);
        $fqcn = $this->mapperNamespace.'\\'.$type;

        if (isset($this->dataMapperMatchers[$type])) {
            $matchedFqcn = $this->dataMapperMatchers[$type]($data);

            if (!$matchedFqcn) {
                return $fqcn;
            }

            if (!class_exists($matchedFqcn, true)) {
                throw new \RuntimeException(sprintf('Mapper class "%s" does not exist.', $matchedFqcn));
            }

            return $matchedFqcn;
        }

        return $fqcn;
    }

    /**
     * Sets a callable which will receive raw data (the JSON payload
     * converted to a PHP array) and will determine the FQCN
     * for appropriate mapping of that resource.
     *
     * @param string        $type              The system type as defined in ResourceBuilder::getSystemType()
     * @param callable|null $dataMapperMatcher A valid callable
     *
     * @return static
     */
    public function setDataMapperMatcher($type, ?callable $dataMapperMatcher = null)
    {
        $this->dataMapperMatchers[$type] = $dataMapperMatcher;

        return $this;
    }

    /**
     * Returns the namespace where mapper classes are located.
     *
     * @return string
     */
    abstract protected function getMapperNamespace();

    /**
     * Abstract function for instantiating a mapper.
     * This function is made abstract because different resource builders
     * might need to create mappers with different arguments.
     *
     * @param string $fqcn
     *
     * @return MapperInterface
     */
    abstract protected function createMapper($fqcn);

    /**
     * Determines the SDK resource type given the API system type.
     *
     * @param array $data The raw data fetched from the API
     *
     * @throws \InvalidArgumentException If the data array provided doesn't contain meaningful information
     *
     * @return string The system type that works in the SDK
     */
    abstract protected function getSystemType(array $data);
}
