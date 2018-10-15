<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2018 Contentful GmbH
 * @license   MIT
 */

declare(strict_types=1);

namespace Contentful\Core\Api;

use Contentful\Core\Resource\ResourceInterface;

interface ClientInterface
{
    /**
     * Sends a request to the API, and returns a resource object.
     *
     * @param string $method  The HTTP method
     * @param string $uri     The URI path
     * @param array  $options An array of optional parameters. The following keys are accepted:
     *                        * query   An array of query parameters that will be appended to the URI
     *                        * headers An array of headers that will be added to the request
     *                        * body    The request body
     *                        * host    A string that can be used to override the default client base URI
     *
     * @throws \RuntimeException
     *
     * @return ResourceInterface
     */
    public function request(string $method, string $uri, array $options = []): ResourceInterface;

    /**
     * Set the application name and version.
     * The values are used as part of the X-Contentful-User-Agent header.
     *
     * @param string $name
     * @param string $version
     */
    public function setApplication(string $name, string $version = '');

    /**
     * Set the integration name and version.
     * The values are used as part of the X-Contentful-User-Agent header.
     *
     * @param string $name
     * @param string $version
     */
    public function setIntegration(string $name, string $version = '');

    /**
     * Returns a string representation of the API currently in use.
     *
     * @return string
     */
    public function getApi(): string;
}
