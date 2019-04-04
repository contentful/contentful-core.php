<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2019 Contentful GmbH
 * @license   MIT
 */

declare(strict_types=1);

namespace Contentful\Core\Api;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Psr7\UriResolver;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

/**
 * RequestBuilder class.
 *
 * This class provides a convenient way of creating a PSR-7 request object.
 */
class RequestBuilder
{
    /**
     * @var string
     */
    private $accessToken;

    /**
     * @var Uri
     */
    private $host;

    /**
     * @var string
     */
    private $apiContentType;

    /**
     * @var UserAgentGenerator
     */
    private $userAgentGenerator;

    /**
     * @param string             $accessToken
     * @param string             $host
     * @param string             $apiContentType
     * @param UserAgentGenerator $userAgentGenerator
     */
    public function __construct(
        string $accessToken,
        string $host,
        string $apiContentType,
        UserAgentGenerator $userAgentGenerator
    ) {
        $this->accessToken = $accessToken;
        $this->host = new Uri($host);
        $this->userAgentGenerator = $userAgentGenerator;
        $this->apiContentType = $apiContentType;
    }

    /**
     * @param string $method
     * @param string $path
     * @param array  $options
     *
     * @return RequestInterface
     */
    public function build(string $method, string $path, array $options): RequestInterface
    {
        $body = $options['body'] ?? null;

        $uri = $this->getUri(
            $path,
            $options['host'] ?? null,
            $options['query'] ?? []
        );

        $headers = $this->getHeaders(
            $options['headers'] ?? [],
            $body
        );

        return new Request($method, $uri, $headers, $body);
    }

    /**
     * @param string      $path
     * @param string|null $host
     * @param string[]    $query
     *
     * @return UriInterface
     */
    private function getUri(string $path, string $host = null, array $query = []): UriInterface
    {
        $host = $host ? new Uri($host) : $this->host;
        $uri = UriResolver::resolve($host, new Uri($path));

        if ($query) {
            $uri = $uri->withQuery(\http_build_query(
                $query,
                '',
                '&',
                \PHP_QUERY_RFC3986
            ));
        }

        return $uri;
    }

    /**
     * @param string[] $userHeaders
     * @param mixed    $body
     *
     * @return string[]
     */
    private function getHeaders(array $userHeaders, $body): array
    {
        $headers = [
            'X-Contentful-User-Agent' => $this->userAgentGenerator->getUserAgent(),
            'Accept' => $this->apiContentType,
            'Accept-Encoding' => 'gzip',
            'Authorization' => 'Bearer '.$this->accessToken,
        ];

        if ($body) {
            $headers['Content-Type'] = $this->apiContentType;
        }

        return \array_merge($headers, $userHeaders);
    }
}
