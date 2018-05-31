<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2018 Contentful GmbH
 * @license   MIT
 */

namespace Contentful\Core\Api;

use Contentful\Core\Log\NullLogger;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\RequestInterface;
use Psr\Log\LoggerInterface;

/**
 * Abstract client for common code for the different clients.
 */
abstract class BaseClient
{
    /**
     * @var HttpClient
     */
    private $httpClient;

    /**
     * @var LoggerInterface
     */
    private $logger;

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
     * @param string               $baseUri
     * @param LoggerInterface|null $logger
     * @param HttpClient|null      $httpClient
     */
    public function __construct($accessToken, $baseUri, LoggerInterface $logger = null, HttpClient $httpClient = null)
    {
        $this->logger = $logger ?: new NullLogger();
        $this->httpClient = $httpClient ?: new HttpClient();

        $this->userAgentGenerator = new UserAgentGenerator(
            $this->getSdkName(),
            $this->getSdkVersion()
        );
        $this->requestBuilder = new RequestBuilder(
            $accessToken,
            $baseUri,
            $this->getApiContentType(),
            $this->userAgentGenerator
        );
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
        $this->userAgentGenerator->setApplication($name, $version);

        return $this;
    }

    /**
     * Set the integration name and version. The values are used as part of the X-Contentful-User-Agent header.
     *
     * @param string|null $name
     * @param string|null $version
     *
     * @return $this
     */
    public function setIntegration($name, $version = null)
    {
        $this->userAgentGenerator->setIntegration($name, $version);

        return $this;
    }

    /**
     * @param string $method  The HTTP method
     * @param string $path    The URI path
     * @param array  $options An array of optional parameters. The following keys are accepted:
     *                        * query   An array of query parameters that will be appended to the URI
     *                        * headers An array of headers that will be added to the request
     *                        * body    The request body
     *                        * baseUri A string that can be used to override the default client base URI
     *
     * @return array|null
     */
    protected function request($method, $path, array $options = [])
    {
        $request = $this->requestBuilder->build($method, $path, $options);

        $message = $this->callApi($request);
        $this->messages[] = $message;

        $this->logger->log(
            $message->getLogLevel(),
            $message->asString()
        );

        if ($message->getException()) {
            throw $message->getException();
        }

        $body = $message->getResponse()
            ? (string) $message->getResponse()->getBody()
            : null;

        return $body
            ? \GuzzleHttp\json_decode((string) $body, true)
            : [];
    }

    /**
     * Performs a query to the API, and returns a message object
     * which contains all information needed for processing and logging.
     *
     * @param RequestInterface $request
     *
     * @return Message
     */
    private function callApi(RequestInterface $request)
    {
        $startTime = \microtime(true);

        $exception = null;
        try {
            $response = $this->httpClient->send($request);
        } catch (ClientException $exception) {
            $response = $exception->hasResponse()
                ? $exception->getResponse()
                : null;

            $exception = $this->createCustomException($exception);
        }

        $duration = \microtime(true) - $startTime;

        return new Message(
            $this->getApi(),
            $duration,
            $request,
            $response,
            $exception
        );
    }

    /**
     * Attempts to create a custom exception.
     * It will return a default exception if no suitable class is found.
     *
     * @param ClientException $exception
     *
     * @return Exception
     */
    private function createCustomException(ClientException $exception)
    {
        $errorId = null;
        $response = $exception->getResponse();
        if ($response) {
            $data = \GuzzleHttp\json_decode($response->getBody(), true);
            $errorId = $data['sys']['id'];
        }

        $exceptionClass = $this->getExceptionClass($errorId);

        return new $exceptionClass($exception);
    }

    /**
     * Returns the FQCN of an exception class to be used for the given API error.
     *
     * @param string $apiError
     *
     * @return string
     */
    private function getExceptionClass($apiError)
    {
        $namespace = $this->getExceptionNamespace();
        if ($namespace) {
            $class = $namespace.'\\'.$apiError.'Exception';

            if (\class_exists($class)) {
                return $class;
            }
        }

        $class = '\\Contentful\\Core\\Exception\\'.$apiError.'Exception';

        return \class_exists($class) ? $class : Exception::class;
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
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @return Message[]
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * Returns a string representation of the API currently in use.
     *
     * @return string
     */
    abstract public function getApi();

    /**
     * The name of the library to be used in the User-Agent header.
     *
     * @return string
     */
    abstract protected function getSdkName();

    /**
     * The version of the library to be used in the User-Agent header.
     *
     * @return string
     */
    abstract protected function getSdkVersion();

    /**
     * Returns the Content-Type (MIME-Type) to be used when communication with the API.
     *
     * @return string
     */
    abstract protected function getApiContentType();
}
