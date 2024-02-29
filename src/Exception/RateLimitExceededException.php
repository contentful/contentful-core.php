<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2024 Contentful GmbH
 * @license   MIT
 */

declare(strict_types=1);

namespace Contentful\Core\Exception;

use Contentful\Core\Api\Exception;
use GuzzleHttp\Exception\RequestException;

/**
 * A RateLimitExceededException is thrown when there have been too many requests.
 *
 * The usual RateLimit on the Content Delivery API is 216000 requests/hour and 78 requests/second.
 * Responses cached by the Contentful CDN don't count against the rate limit.
 */
class RateLimitExceededException extends Exception
{
    /**
     * @var int|null
     */
    private $rateLimitReset;

    /**
     * RateLimitExceededException constructor.
     */
    public function __construct(RequestException $previous, string $message = '')
    {
        parent::__construct($previous, $message);

        $response = $this->getResponse();
        if ($response) {
            $this->rateLimitReset = (int) $response->getHeader('X-Contentful-RateLimit-Reset')[0];
        }
    }

    /**
     * Returns the number of seconds until the rate limit expires.
     *
     * @return int|null
     */
    public function getRateLimitReset()
    {
        return $this->rateLimitReset;
    }
}
