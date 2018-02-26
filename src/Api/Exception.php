<?php

/**
 * This file is part of the contentful-core.php package.
 *
 * @copyright 2015-2018 Contentful GmbH
 * @license   MIT
 */

namespace Contentful\Core\Api;

use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * An Exception is thrown when an errors occurs while communicating with the API.
 */
class Exception extends \RuntimeException implements \Serializable
{
    /**
     * @var RequestException|null
     */
    private $previous;

    /**
     * @var string|null
     */
    private $requestId = null;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var ResponseInterface|null
     */
    private $response;

    /**
     * Exception constructor.
     *
     * @param RequestException $previous
     * @param string           $message
     */
    public function __construct(RequestException $previous, $message = '')
    {
        $this->previous = $previous;
        $this->request = $previous->getRequest();
        $this->response = $previous->getResponse();

        if ($this->response) {
            $this->requestId = $this->response->getHeaderLine('X-Contentful-Request-Id');
        }

        if ('' === $message) {
            $message = self::createExceptionMessage($previous, $this->response);
        }

        parent::__construct($message, 0, $previous);
    }

    public function serialize()
    {
        return \serialize([
            'message' => $this->message,
            'code' => $this->code,
            'file' => $this->message,
            'line' => $this->line,
            'requestId' => $this->requestId,
            'request' => \GuzzleHttp\Psr7\str($this->request),
            'response' => $this->response ? \GuzzleHttp\Psr7\str($this->response) : null,
        ]);
    }

    public function unserialize($serialized)
    {
        $data = \unserialize($serialized);

        $this->message = $data['message'];
        $this->code = $data['code'];
        $this->file = $data['file'];
        $this->line = $data['line'];
        $this->requestId = $data['requestId'];
        $this->request = \GuzzleHttp\Psr7\parse_request($data['request']);
        $this->response = $data['response'] ? \GuzzleHttp\Psr7\parse_response($data['response']) : null;
    }

    private static function createExceptionMessage(RequestException $previous, ResponseInterface $response = null)
    {
        if (!$response) {
            return $previous->getMessage();
        }

        try {
            $result = \GuzzleHttp\json_decode($response->getBody(), true);
            if (isset($result['message'])) {
                return $result['message'];
            }
        } catch (\InvalidArgumentException $e) {
            return $previous->getMessage();
        }

        return $previous->getMessage();
    }

    /**
     * Get the request that caused the exception.
     *
     * @return RequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Get the associated response.
     *
     * @return ResponseInterface|null
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Check if a response was received.
     *
     * @return bool
     */
    public function hasResponse()
    {
        return null !== $this->response;
    }

    /**
     * @return string|null
     */
    public function getRequestId()
    {
        return $this->requestId;
    }
}
