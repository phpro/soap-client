<?php

namespace Phpro\SoapClient\Soap\HttpBinding;

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
