<?php

namespace Phpro\SoapClient\Soap\HttpBinding;

use Http\Message\Formatter\FullHttpMessageFormatter;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class LastRequestInfo
 *
 * @package Phpro\SoapClient\Soap\HttpBinding
 */
class LastRequestInfo
{
    /**
     * @var string
     */
    private $lastRequestHeaders;

    /**
     * @var string
     */
    private $lastRequest;

    /**
     * @var string
     */
    private $lastResponseHeaders;

    /**
     * @var string
     */
    private $lastResponse;

    /**
     * LastRequestInfo constructor.
     *
     * @param string $lastRequestHeaders
     * @param string $lastRequest
     * @param string $lastResponseHeaders
     * @param string $lastResponse
     */
    public function __construct(
        string $lastRequestHeaders,
        string $lastRequest,
        string $lastResponseHeaders,
        string $lastResponse
    ) {
        $this->lastRequestHeaders = $lastRequestHeaders;
        $this->lastRequest = $lastRequest;
        $this->lastResponseHeaders = $lastResponseHeaders;
        $this->lastResponse = $lastResponse;
    }

    /**
     * @param \SoapClient $soapClient
     *
     * @return LastRequestInfo
     */
    public static function createFromSoapClient(\SoapClient $soapClient): self
    {
        return new self(
            (string) $soapClient->__getLastRequestHeaders(),
            (string) $soapClient->__getLastRequest(),
            (string) $soapClient->__getLastResponseHeaders(),
            (string) $soapClient->__getLastResponse()
        );
    }

    /**
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     *
     * @return LastRequestInfo
     */
    public static function createFromPsr7RequestAndResponse(
        RequestInterface $request,
        ResponseInterface $response
    ): self {
        // Reset the bodies:
        $request->getBody()->rewind();
        $response->getBody()->rewind();

        $formatter = new FullHttpMessageFormatter(null);
        $requestString = $formatter->formatRequest($request);
        $responseString = $formatter->formatResponse($response);

        $requestHeaders = '';
        $requestBody = '';
        $responseHeaders = '';
        $responseBody = '';

        if ($requestString) {
            $requestParts = explode(
                "\n\n",
                substr($requestString, strpos($requestString, "\n") + 1),
                2
            );

            $requestHeaders = trim($requestParts[0] ?? '');
            $requestBody = $requestParts[1] ?? '';
        }

        if ($responseString) {
            $responseParts = explode(
                "\n\n",
                substr($responseString, strpos($responseString, "\n") + 1),
                2
            );

            $responseHeaders = trim($responseParts[0] ?? '');
            $responseBody = $responseParts[1] ?? '';
        }

        // Reset the bodies:
        $request->getBody()->rewind();
        $response->getBody()->rewind();

        return new self(
            $requestHeaders,
            $requestBody,
            $responseHeaders,
            $responseBody
        );
    }

    /**
     * @return LastRequestInfo
     */
    public static function createEmpty(): self
    {
        return new self('', '', '', '');
    }

    /**
     * @return string
     */
    public function getLastRequestHeaders(): string
    {
        return $this->lastRequestHeaders;
    }

    /**
     * @return string
     */
    public function getLastRequest(): string
    {
        return $this->lastRequest;
    }

    /**
     * @return string
     */
    public function getLastResponseHeaders(): string
    {
        return $this->lastResponseHeaders;
    }

    /**
     * @return string
     */
    public function getLastResponse(): string
    {
        return $this->lastResponse;
    }
}
