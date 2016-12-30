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
 * @property string __last_request
 * @property string __last_response
 * @property string __last_request_headers
 * @property string __last_response_headers
 *
 * @package Phpro\SoapClient\Soap
 *
 * Note: Make sure to extend the \SoapClient without alias for php-vcr implementations.
 */
class SoapClient extends \SoapClient
{
    /**
     * SOAP types derived from WSDL
     *
     * @var array
     */
    protected $types;

    /**
     * @var HandlerInterface
     */
    protected $handler;

    /**
     * SoapClient constructor.
     *
     * @param mixed      $wsdl
     * @param array|null $options
     */
    public function __construct($wsdl, array $options = null)
    {
        parent::__construct($wsdl, $options);

        // Use the SoapHandle by default.
        $this->handler = new SoapHandle($this);
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
    public function getSoapTypes()
    {
        if ($this->types) {
            return $this->types;
        }

        $soapTypes = $this->__getTypes();
        foreach ($soapTypes as $soapType) {
            $properties = array();
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
                $properties[$matches[2]] = $matches[1];
            }

            $this->types[$typeName] = $properties;
        }

        return $this->types;
    }

    /**
     * Get a SOAP type’s elements
     *
     * @param string $type Object name
     * @return array       Elements for the type
     */

    /**
     * Get SOAP elements for a complexType
     *
     * @param string $complexType Name of SOAP complexType
     *
     * @return array  Names of elements and their types
     */
    public function getSoapElements($complexType)
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
    public function getSoapElementType($complexType, $element)
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
    public function doInternalRequest($request, $location, $action, $version, $oneWay = 0)
    {
        return (string) parent::__doRequest($request, $location, $action, $version, $oneWay);
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
        $this->__last_request = (string) $lastRequestInfo->getLastRequest();
        $this->__last_response = (string) $lastRequestInfo->getLastResponse();
        $this->__last_request_headers = (string) $lastRequestInfo->getLastRequestHeaders();
        $this->__last_response_headers = (string) $lastRequestInfo->getLastResponseHeaders();

        // Return the response or an empty response when oneWay is enabled.
        return $oneWay ? null : $response->getResponse();
    }
}
