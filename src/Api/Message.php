<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2019 Contentful GmbH
 * @license   MIT
 */

declare(strict_types=1);

namespace Contentful\Core\Api;

use function GuzzleHttp\json_decode as guzzle_json_decode;
use function GuzzleHttp\json_encode as guzzle_json_encode;
use function GuzzleHttp\Psr7\parse_request as guzzle_parse_request;
use function GuzzleHttp\Psr7\parse_response as guzzle_parse_response;
use function GuzzleHttp\Psr7\str as guzzle_stringify_message;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LogLevel;

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
     * @var float
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
     * @param float                  $duration
     * @param RequestInterface       $request
     * @param ResponseInterface|null $response
     * @param Exception|null         $exception
     */
    public function __construct(
        string $api,
        float $duration,
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
    public static function createFromString(string $json): self
    {
        $data = guzzle_json_decode($json, true);

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
            guzzle_parse_request($data['request']),
            $data['response'] ? guzzle_parse_response($data['response']) : null,
            $data['exception'] ? \unserialize($data['exception']) : null
        );
    }

    /**
     * @return string
     */
    public function getLogLevel(): string
    {
        return $this->isError()
            ? LogLevel::ERROR
            : LogLevel::INFO;
    }

    /**
     * @return string
     */
    public function getApi(): string
    {
        return $this->api;
    }

    /**
     * @return RequestInterface
     */
    public function getRequest(): RequestInterface
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
     * The duration in microseconds.
     *
     * @return float
     */
    public function getDuration(): float
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
    public function isError(): bool
    {
        return null !== $this->exception;
    }

    /**
     * @return array
     */
    private function asSerializableArray(): array
    {
        return [
            'api' => $this->api,
            'duration' => $this->duration,
            'request' => guzzle_stringify_message($this->request),
            'response' => null !== $this->response ? guzzle_stringify_message($this->response) : null,
            'exception' => \serialize($this->exception),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize(): array
    {
        return $this->asSerializableArray();
    }

    /**
     * {@inheritdoc}
     */
    public function serialize(): string
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
        $this->request = guzzle_parse_request($data['request']);
        $this->response = null !== $data['response'] ? guzzle_parse_response($data['response']) : null;
        $this->exception = \unserialize($data['exception']);
    }

    /**
     * Returns a string representation of the current message.
     *
     * @return string
     */
    public function asString(): string
    {
        return guzzle_json_encode($this);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->asString();
    }
}
