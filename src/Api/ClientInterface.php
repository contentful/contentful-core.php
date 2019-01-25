<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2019 Contentful GmbH
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
     * Sets the current application.
     * The values are used as part of the X-Contentful-User-Agent header.
     *
     * @param ApplicationInterface $application
     */
    public function useApplication(ApplicationInterface $application);

    /**
     * Set the application name and version.
     * The values are used as part of the X-Contentful-User-Agent header.
     *
     * @param string $name
     * @param string $version
     *
     * @deprecated 2.2.0 Use useApplication instead
     */
    public function setApplication(string $name, string $version = '');

    /**
     * Sets the current integration.
     * The values are used as part of the X-Contentful-User-Agent header.
     *
     * @param IntegrationInterface $integration
     */
    public function useIntegration(IntegrationInterface $integration);

    /**
     * Set the integration name and version.
     * The values are used as part of the X-Contentful-User-Agent header.
     *
     * @param string $name
     * @param string $version
     *
     * @deprecated 2.2.0 Use useIntegration instead
     */
    public function setIntegration(string $name, string $version = '');

    /**
     * Returns a string representation of the API currently in use.
     *
     * @return string
     */
    public function getApi(): string;
}
