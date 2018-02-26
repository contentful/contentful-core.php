<?php

/**
 * This file is part of the contentful-core.php package.
 *
 * @copyright 2015-2018 Contentful GmbH
 * @license   MIT
 */

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
    private $baseUri;

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
     * @param string             $baseUri
     * @param string             $apiContentType
     * @param UserAgentGenerator $userAgentGenerator
     */
    public function __construct($accessToken, $baseUri, $apiContentType, UserAgentGenerator $userAgentGenerator)
    {
        $this->accessToken = $accessToken;
        $this->baseUri = new Uri($baseUri);
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
    public function build($method, $path, array $options)
    {
        $body = isset($options['body']) ? $options['body'] : null;

        $uri = $this->getUri(
            $path,
            isset($options['baseUri']) ? $options['baseUri'] : null,
            isset($options['query']) ? $options['query'] : null
        );

        $headers = $this->getHeaders(
            isset($options['headers']) ? $options['headers'] : [],
            $body
        );

        return new Request($method, $uri, $headers, $body);
    }

    /**
     * @param string      $path
     * @param string|null $baseUri
     * @param string|null $query
     *
     * @return UriInterface
     */
    private function getUri($path, $baseUri, $query)
    {
        $baseUri = $baseUri ? new Uri($baseUri) : $this->baseUri;

        $uri = UriResolver::resolve($baseUri, new Uri($path));

        if ($query) {
            $serializedQuery = \http_build_query($query, null, '&', \PHP_QUERY_RFC3986);
            $uri = $uri->withQuery($serializedQuery);
        }

        return $uri;
    }

    /**
     * @param string[] $userHeaders
     * @param mixed    $body
     *
     * @return string[]
     */
    private function getHeaders(array $userHeaders, $body)
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
