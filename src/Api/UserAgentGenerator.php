<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2019 Contentful GmbH
 * @license   MIT
 */

declare(strict_types=1);

namespace Contentful\Core\Api;

/**
 * UserAgentGenerator class.
 *
 * This class is responsible for generating the "X-Contentful-User-Agent" header,
 * which collects the PHP version, SDK version, and possibly application and integration
 * names and versions.
 */
class UserAgentGenerator
{
    /**
     * @var string
     */
    private $sdk;

    /**
     * @var string
     */
    private $application = '';

    /**
     * @var string
     */
    private $integration = '';

    /**
     * @var string|null
     */
    private $cachedUserAgent;

    /**
     * UserAgentGenerator constructor.
     *
     * @param string $name
     * @param string $version
     */
    public function __construct(string $name, string $version)
    {
        $this->sdk = $name.'/'.$version;
    }

    /**
     * Set the application name and version.
     * The values are used as part of the X-Contentful-User-Agent header.
     *
     * @param string $name
     * @param string $version
     *
     * @return $this
     */
    public function setApplication(string $name, string $version = '')
    {
        $this->application = $name.($version ? '/'.$version : '');

        // Reset the cached value
        $this->cachedUserAgent = null;

        return $this;
    }

    /**
     * Set the application name and version.
     * The values are used as part of the X-Contentful-User-Agent header.
     *
     * @param string $name
     * @param string $version
     *
     * @return $this
     */
    public function setIntegration(string $name, string $version = '')
    {
        $this->integration = $name.($version ? '/'.$version : '');

        // Reset the cached value
        $this->cachedUserAgent = null;

        return $this;
    }

    /**
     * @return string
     */
    private function generate(): string
    {
        $possibleOS = [
            'WINNT' => 'Windows',
            'Darwin' => 'macOS',
        ];

        $parts = \array_filter([
            'app' => $this->application,
            'integration' => $this->integration,
            'sdk' => $this->sdk,
            'platform' => \sprintf(
                'PHP/%d.%d.%d',
                \PHP_MAJOR_VERSION,
                \PHP_MINOR_VERSION,
                \PHP_RELEASE_VERSION
            ),
            'os' => $possibleOS[\PHP_OS] ?? 'Linux',
        ]);

        $userAgent = '';
        foreach ($parts as $key => $value) {
            $userAgent .= $key.' '.$value.'; ';
        }

        return \trim($userAgent);
    }

    /**
     * Returns the value of the User-Agent header for any requests made to Contentful.
     *
     * @return string
     */
    public function getUserAgent(): string
    {
        if (null === $this->cachedUserAgent) {
            $this->cachedUserAgent = $this->generate();
        }

        return $this->cachedUserAgent;
    }
}
