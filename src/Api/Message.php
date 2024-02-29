<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2024 Contentful GmbH
 * @license   MIT
 */

declare(strict_types=1);

namespace Contentful\Core\Api;

use GuzzleHttp\Psr7\Message as GuzzleMessage;
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
     */
    public function __construct(
        string $api,
        float $duration,
        RequestInterface $request,
        ?ResponseInterface $response = null,
        ?Exception $exception = null
    ) {
        if (!\in_array($api, ['DELIVERY', 'PREVIEW', 'MANAGEMENT'], true)) {
            throw new \InvalidArgumentException(sprintf('Unknown API value "%s".', $api));
        }

        $this->api = $api;
        $this->request = $request;
        $this->response = $response;
        $this->duration = $duration;
        $this->exception = $exception;
    }

    /**
     * Creates a new instance of the class from a JSON string.
     */
    public static function createFromString(string $json): self
    {
        $data = json_decode($json, true);

        if (!\is_array($data)
            || !isset($data['api'])
            || !isset($data['request'])
            || !isset($data['response'])
            || !isset($data['duration'])
            || !isset($data['exception'])
        ) {
            throw new \InvalidArgumentException('String passed to Message::createFromString() is valid JSON but does not contain required fields.');
        }

        return new self(
            $data['api'],
            $data['duration'],
            GuzzleMessage::parseRequest($data['request']),
            $data['response'] ? GuzzleMessage::parseResponse($data['response']) : null,
            $data['exception'] ? unserialize($data['exception']) : null
        );
    }

    public function getLogLevel(): string
    {
        return $this->isError()
            ? LogLevel::ERROR
            : LogLevel::INFO;
    }

    public function getApi(): string
    {
        return $this->api;
    }

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
     */
    public function isError(): bool
    {
        return null !== $this->exception;
    }

    private function asSerializableArray(): array
    {
        return [
            'api' => $this->api,
            'duration' => $this->duration,
            'request' => GuzzleMessage::toString($this->request),
            'response' => null !== $this->response ? GuzzleMessage::toString($this->response) : null,
            'exception' => serialize($this->exception),
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->asSerializableArray();
    }

    public function serialize(): string
    {
        return serialize($this->asSerializableArray());
    }

    public function unserialize($serialized)
    {
        $data = unserialize($serialized);

        $this->api = $data['api'];
        $this->duration = $data['duration'];
        $this->request = GuzzleMessage::parseRequest($data['request']);
        $this->response = null !== $data['response'] ? GuzzleMessage::parseResponse($data['response']) : null;
        $this->exception = unserialize($data['exception']);
    }

    /**
     * Returns a string representation of the current message.
     */
    public function asString(): string
    {
        return json_encode($this);
    }

    public function __toString(): string
    {
        return $this->asString();
    }

    public function __serialize(): array
    {
        return [
            'api' => $this->api,
            'duration' => $this->duration,
            'request' => $this->request,
            'response' => $this->response,
            'exception' => $this->exception,
        ];
    }

    public function __unserialize(array $data): void
    {
        $this->api = $data['api'];
        $this->duration = $data['duration'];
        $this->request = $data['request'];
        $this->response = $data['response'];
        $this->exception = $data['exception'];
    }
}
