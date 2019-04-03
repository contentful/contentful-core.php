<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2019 Contentful GmbH
 * @license   MIT
 */

declare(strict_types=1);

namespace Contentful\Core\Api;

use Contentful\Core\Log\NullLogger;
use GuzzleHttp\Client as HttpClient;
use function GuzzleHttp\json_decode as guzzle_json_decode;
use Jean85\PrettyVersions;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Abstract client for common code for the different clients.
 */
abstract class BaseClient implements ClientInterface
{
    /**
     * @var Requester
     */
    private $requester;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var string
     */
    private $host;

    /**
     * @var UserAgentGenerator
     */
    private $userAgentGenerator;

    /**
     * @var RequestBuilder
     */
    private $requestBuilder;

    /**
     * @var Message[]
     */
    private $messages = [];

    /**
     * Client constructor.
     *
     * @param string               $accessToken
     * @param string               $host
     * @param LoggerInterface|null $logger
     * @param HttpClient|null      $httpClient
     */
    public function __construct(
        string $accessToken,
        string $host,
        LoggerInterface $logger = null,
        HttpClient $httpClient = null
    ) {
        $this->logger = $logger ?: new NullLogger();
        $this->requester = new Requester(
            $httpClient ?: new HttpClient(),
            $this->getApi(),
            $this->getExceptionNamespace()
        );

        if ('/' === \mb_substr($host, -1)) {
            $host = \mb_substr($host, 0, -1);
        }
        $this->host = $host;

        $this->userAgentGenerator = new UserAgentGenerator($this->getSdkName(), self::getVersion());
        $this->requestBuilder = new RequestBuilder(
            $accessToken,
            $host,
            $this->getApiContentType(),
            $this->userAgentGenerator
        );
    }

    /**
     * Make a call to the API and returns the parsed JSON.
     *
     * @param string $method  The HTTP method
     * @param string $uri     The URI path
     * @param array  $options An array of optional parameters. The following keys are accepted:
     *                        * query   An array of query parameters that will be appended to the URI
     *                        * headers An array of headers that will be added to the request
     *                        * body    The request body
     *                        * host    A string that can be used to override the default client base URI
     *
     * @throws \Exception
     *
     * @return array
     */
    protected function callApi(string $method, string $uri, array $options = []): array
    {
        $request = $this->requestBuilder->build($method, $uri, $options);

        $message = $this->requester->sendRequest($request);
        $this->messages[] = $message;

        $this->logMessage($message);

        $exception = $message->getException();
        if (null !== $exception) {
            throw $exception;
        }

        return $this->parseResponse($message->getResponse());
    }

    /**
     * Write information about a message object into the logger.
     *
     * @param Message $message
     */
    private function logMessage(Message $message)
    {
        $logMessage = \sprintf(
            '%s %s (%.3Fs)',
            $message->getRequest()->getMethod(),
            (string) $message->getRequest()->getUri(),
            $message->getDuration()
        );

        // This is a "simple" log, for general purpose
        $this->logger->log(
            $message->getLogLevel(),
            $logMessage
        );

        // This is a debug log, with all message details, useful for debugging
        $this->logger->debug($logMessage, $message->jsonSerialize());
    }

    /**
     * Parse the body of a JSON response.
     *
     * @param ResponseInterface|null $response
     *
     * @return array
     */
    private function parseResponse(ResponseInterface $response = null): array
    {
        $body = $response
            ? (string) $response->getBody()
            : null;

        return $body
            ? guzzle_json_decode($body, true)
            : [];
    }

    /**
     * Override this method for registering a custom namespace where the Client
     * will look for an exception. If no exception is found in the custom namespace,
     * the default namespace will be used.
     *
     * @return string|null
     */
    protected function getExceptionNamespace()
    {
        return null;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * Returns an array of Message objects.
     * This can be used to inspect all API calls that have been made by the current client.
     *
     * @return Message[]
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * {@inheritdoc}
     */
    public function useApplication(ApplicationInterface $application)
    {
        $version = $application->isPackagedApplication()
            ? self::getVersionForPackage($application->getApplicationPackageName())
            : $application->getApplicationVersion();

        $this->userAgentGenerator->setApplication(
            $application->getApplicationName(),
            $version
        );
    }

    /**
     * {@inheritdoc}
     */
    public function setApplication(string $name, string $version = '')
    {
        $this->userAgentGenerator->setApplication($name, $version);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function useIntegration(IntegrationInterface $integration)
    {
        $this->userAgentGenerator->setIntegration(
            $integration->getIntegrationName(),
            self::getVersionForPackage($integration->getIntegrationPackageName())
        );
    }

    /**
     * {@inheritdoc}
     */
    public function setIntegration(string $name, string $version = '')
    {
        $this->userAgentGenerator->setIntegration($name, $version);

        return $this;
    }

    /**
     * @return string
     */
    public static function getVersion(): string
    {
        return self::getVersionForPackage(static::getPackageName());
    }

    /**
     * @param string $package
     *
     * @return string
     */
    protected static function getVersionForPackage(string $package): string
    {
        try {
            $shortVersion = PrettyVersions::getVersion($package)
                ->getShortVersion()
            ;

            // Removes the ".x-dev" part which is inserted during development
            if ('.x-dev' === \mb_substr($shortVersion, -6)) {
                $shortVersion = \mb_substr($shortVersion, 0, -6).'-dev';
            }

            return $shortVersion;
        } catch (\OutOfBoundsException $exception) {
            return '0.0.0-alpha';
        }
    }

    /**
     * Returns the packagist name of the current package.
     *
     * @return string
     */
    abstract protected static function getPackageName(): string;

    /**
     * The name of the library to be used in the User-Agent header.
     *
     * @return string
     */
    abstract protected static function getSdkName(): string;

    /**
     * Returns the Content-Type (MIME-Type) to be used when communication with the API.
     *
     * @return string
     */
    abstract protected static function getApiContentType(): string;
}
