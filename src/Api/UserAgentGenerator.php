<?php

/**
 * This file is part of the contentful-core.php package.
 *
 * @copyright 2015-2018 Contentful GmbH
 * @license   MIT
 */

namespace Contentful\Core\Api;

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
    public function __construct($name, $version)
    {
        $this->sdk = $name.'/'.$version;
    }

    /**
     * Set the application name and version. The values are used as part of the X-Contentful-User-Agent header.
     *
     * @param string|null $name
     * @param string|null $version
     *
     * @return $this
     */
    public function setApplication($name, $version = null)
    {
        $this->application = $name ? $name.($version ? '/'.$version : '') : '';

        // Reset the cached value
        $this->cachedUserAgent = null;

        return $this;
    }

    /**
     * Set the application name and version. The values are used as part of the X-Contentful-User-Agent header.
     *
     * @param string|null $name
     * @param string|null $version
     *
     * @return $this
     */
    public function setIntegration($name, $version = null)
    {
        $this->integration = $name ? $name.($version ? '/'.$version : '') : '';

        // Reset the cached value
        $this->cachedUserAgent = null;

        return $this;
    }

    /**
     * @return string
     */
    private function generate()
    {
        $possibleOS = [
            'WINNT' => 'Windows',
            'Darwin' => 'macOS',
        ];

        $parts = \array_filter([
            'app' => $this->application,
            'integration' => $this->integration,
            'sdk' => $this->sdk,
            'platform' => 'PHP/'.\PHP_MAJOR_VERSION.'.'.\PHP_MINOR_VERSION.'.'.\PHP_RELEASE_VERSION,
            'os' => isset($possibleOS[\PHP_OS]) ? $possibleOS[\PHP_OS] : 'Linux',
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
    public function getUserAgent()
    {
        if (null === $this->cachedUserAgent) {
            $this->cachedUserAgent = $this->generate();
        }

        return $this->cachedUserAgent;
    }
}
