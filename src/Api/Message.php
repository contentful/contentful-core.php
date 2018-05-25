<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2018 Contentful GmbH
 * @license   MIT
 */

namespace Contentful\Core\Api;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Message class.
 *
 * This class is a representation of a log message which contains
 * API-related information and can easily be serialized.
 */
class Message implements \Serializable, \JsonSerializable
{
    /**
     * @var string
     */
    private $api;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var ResponseInterface|null
     */
    private $response;

    /**
     * @var float|null
     */
    private $duration;

    /**
     * @var Exception|null
     */
    private $exception;

    /**
     * Constructor.
     *
     * @param string                 $api
     * @param float|null             $duration
     * @param RequestInterface       $request
     * @param ResponseInterface|null $response
     * @param Exception|null         $exception
     */
    public function __construct(
        $api,
        $duration,
        RequestInterface $request,
        ResponseInterface $response = null,
        Exception $exception = null
    ) {
        if (!\in_array($api, ['DELIVERY', 'PREVIEW', 'MANAGEMENT'], true)) {
            throw new \InvalidArgumentException(\sprintf(
                'Unknown API value "%s".',
                $api
            ));
        }

        $this->api = $api;
        $this->request = $request;
        $this->response = $response;
        $this->duration = $duration;
        $this->exception = $exception;
    }

    /**
     * Creates a new instance of the class from a JSON string.
     *
     * @param string $json
     *
     * @return self
     */
    public static function createFromString($json)
    {
        $data = \GuzzleHttp\json_decode($json, true);

        if (
            !isset($data['api']) ||
            !isset($data['request']) ||
            !isset($data['response']) ||
            !isset($data['duration']) ||
            !isset($data['exception'])
        ) {
            throw new \InvalidArgumentException(
                'String passed to Message::createFromString() is valid JSON but does not contain required fields.'
            );
        }

        return new self(
            $data['api'],
            $data['duration'],
            \GuzzleHttp\Psr7\parse_request($data['request']),
            $data['response'] ? \GuzzleHttp\Psr7\parse_response($data['response']) : null,
            $data['exception'] ? \unserialize($data['exception']) : null
        );
    }

    /**
     * @return string
     */
    public function getLogLevel()
    {
        return $this->isError()
            ? 'ERROR'
            : 'INFO';
    }

    /**
     * @return string
     */
    public function getApi()
    {
        return $this->api;
    }

    /**
     * @return RequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return \Exception|null
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * @return float|null
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @return ResponseInterface|null
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * True if the requests threw an error.
     *
     * @return bool
     */
    public function isError()
    {
        return null !== $this->exception;
    }

    /**
     * @return string[]
     */
    private function asSerializableArray()
    {
        return [
            'api' => $this->api,
            'duration' => $this->duration,
            'request' => \GuzzleHttp\Psr7\str($this->request),
            'response' => null !== $this->response ? \GuzzleHttp\Psr7\str($this->response) : null,
            'exception' => \serialize($this->exception),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return $this->asSerializableArray();
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return \serialize($this->asSerializableArray());
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        $data = \unserialize($serialized);

        $this->api = $data['api'];
        $this->duration = $data['duration'];
        $this->request = \GuzzleHttp\Psr7\parse_request($data['request']);
        $this->response = null !== $data['response'] ? \GuzzleHttp\Psr7\parse_response($data['response']) : null;
        $this->exception = \unserialize($data['exception']);
    }

    /**
     * Returns a string representation of the current message.
     *
     * @return string
     */
    public function asString()
    {
        return \GuzzleHttp\json_encode($this);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->asString();
    }
}
