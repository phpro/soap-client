<?php

/*
 * This file is part of the Phpro application.
 *
 * Copyright (c) 2015-2017 Phpro
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phpro\SoapClient\Soap\HttpBinding;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Request;
use Zend\Diactoros\Response;

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
    public static function createFromSoapClient(\SoapClient $soapClient)
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
    public static function createFromPsr7RequestAndResponse(RequestInterface $request, ResponseInterface $response)
    {
        // Reset the bodies:
        $request->getBody()->rewind();
        $response->getBody()->rewind();

        $requestString = Request\Serializer::toString($request);
        $responseString = Response\Serializer::toString($response);

        $requestHeaders = '';
        $requestBody = '';
        $responseHeaders = '';
        $responseBody = '';

        if ($requestString) {
            $requestParts = explode(
                "\r\n\r\n",
                substr($requestString, strpos($requestString, "\r\n") + 1),
                2
            );

            $requestHeaders = trim($requestParts[0] ?? '');
            $requestBody = $requestParts[1] ?? '';
        }

        if ($responseString) {
            $responseParts = explode(
                "\r\n\r\n",
                substr($responseString, strpos($responseString, "\r\n") + 1),
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
    public static function createEmpty()
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
