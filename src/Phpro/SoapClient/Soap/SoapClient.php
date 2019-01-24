<?php

namespace Phpro\SoapClient\Soap;

use Phpro\SoapClient\Soap\Handler\HandlerInterface;
use Phpro\SoapClient\Soap\Handler\LastRequestInfoCollectorInterface;
use Phpro\SoapClient\Soap\Handler\SoapHandle;
use Phpro\SoapClient\Soap\HttpBinding\LastRequestInfo;
use Phpro\SoapClient\Soap\HttpBinding\SoapRequest;

/**
 * Class SoapClient
 *
 * @package Phpro\SoapClient\Soap
 *
 * Note: Make sure to extend the \SoapClient without alias for php-vcr implementations.
 */
class SoapClient extends \SoapClient
{
    /**
     * @var array
     */
    protected $options = [];

    /**
     * SOAP types derived from WSDLss
     *
     * @var array
     */
    protected $types = [];

    /**
     * @var HandlerInterface
     */
    protected $handler;

    // @codingStandardsIgnoreStart
    /**
     * @var string
     */
    protected $__last_request;

    /**
     * @var string
     */
    protected $__last_response;

    /**
     * @var string
     */
    protected $__last_request_headers;

    /**
     * @var string
     */
    protected $__last_response_headers;
    // @codingStandardsIgnoreEnd

    /**
     * SoapClient constructor.
     *
     * @param mixed $wsdl
     * @param array $options
     */
    public function __construct($wsdl, array $options = [])
    {
        parent::__construct($wsdl, $options);

        // Use the SoapHandle by default.
        $this->handler = new SoapHandle($this);
        $this->options = $options;
    }

    /**
     * @param HandlerInterface $handler
     */
    public function setHandler(HandlerInterface $handler)
    {
        $this->handler = $handler;
    }

    /**
     * Retrieve SOAP types from the WSDL and parse them
     *
     * @return array    Array of types and their properties
     */
    public function getSoapTypes(): array
    {
        if ($this->types) {
            return $this->types;
        }

        $soapTypes = $this->__getTypes();

        $simpleTypes = [
            'base64Binary' => 'string',
        ];
        foreach ($soapTypes as $soapType) {
            if (preg_match('/^(.*) (.*)$/', $soapType, $matches) === 1) {
                $simpleTypes[$matches[2]] = $matches[1];
            }
        }

        foreach ($soapTypes as $soapType) {
            $properties = [];
            $lines = explode("\n", $soapType);
            if (!preg_match('/struct (.*) {/', $lines[0], $matches)) {
                continue;
            }
            $typeName = $matches[1];

            foreach (array_slice($lines, 1) as $line) {
                if ($line === '}') {
                    continue;
                }
                preg_match('/\s* (.*) (.*);/', $line, $matches);

                $properties[$matches[2]] = $simpleTypes[$matches[1]] ?? $matches[1];
            }

            $this->types[$typeName] = $properties;
        }

        return $this->types;
    }

    /**
     * Get SOAP elements for a complexType
     *
     * @param string $complexType Name of SOAP complexType
     *
     * @return array  Names of elements and their types
     */
    public function getSoapElements(string $complexType): array
    {
        $types = $this->getSoapTypes();
        if (isset($types[$complexType])) {
            return $types[$complexType];
        }
    }

    /**
     * Get a SOAP type’s element
     *
     * @param string $complexType Name of SOAP complexType
     * @param string $element     Name of element belonging to SOAP complexType
     *
     * @return string
     */
    public function getSoapElementType(string $complexType, string $element): string
    {
        $elements = $this->getSoapElements($complexType);
        if ($elements && isset($elements[$element])) {
            return $elements[$element];
        }
    }

    /**
     * @param string $request
     * @param string $location
     * @param string $action
     * @param int    $version
     * @param int    $oneWay
     *
     * @return string
     */
    public function doInternalRequest(
        string $request,
        string $location,
        string $action,
        int $version,
        int $oneWay = 0
    ): string {
        return (string)parent::__doRequest($request, $location, $action, $version, $oneWay);
    }

    /**
     * @param string $request
     * @param string $location
     * @param string $action
     * @param int    $version
     * @param int    $oneWay
     *
     * @return string|null
     */
    public function __doRequest($request, $location, $action, $version, $oneWay = 0)
    {
        $request = new SoapRequest($request, $location, $action, $version, $oneWay);
        $response = $this->handler->request($request);

        // Fecth the last request information:
        $lastRequestInfo = LastRequestInfo::createEmpty();
        if ($this->handler instanceof LastRequestInfoCollectorInterface) {
            $lastRequestInfo = $this->handler->collectLastRequestInfo();
        }

        // Copy the request info in the correct internal __last_* parameters:
        // We don't need the trace option: always remember the last response @ request
        $this->__last_request = (string)$lastRequestInfo->getLastRequest() ?? $request;
        $this->__last_response = (string)$lastRequestInfo->getLastResponse() ?? $response->getResponse();
        $this->__last_request_headers = (string)$lastRequestInfo->getLastRequestHeaders();
        $this->__last_response_headers = (string)$lastRequestInfo->getLastResponseHeaders();

        // Return the response or an empty response when oneWay is enabled.
        return $oneWay ? null : $response->getResponse();
    }

    /***
     * @return string
     */
    public function __getLastRequest(): string
    {
        return $this->__last_request ?: (string)parent::__getLastRequest();
    }

    /**
     * @return string
     */
    public function __getLastResponse(): string
    {
        return $this->__last_response ?: (string)parent::__getLastResponse();
    }

    /**
     * @return string
     */
    public function __getLastRequestHeaders(): string
    {
        return $this->__last_request_headers ?: (string)parent::__getLastRequestHeaders();
    }

    /**
     * @return string
     */
    public function __getLastResponseHeaders(): string
    {
        return $this->__last_response_headers ?: (string)parent::__getLastResponseHeaders();
    }
}
