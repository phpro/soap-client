<?php

namespace Phpro\SoapClient\Soap;

use Phpro\SoapClient\Soap\Handler\HandlerInterface;
use Phpro\SoapClient\Soap\Handler\SoapHandle;
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
     * @param int $version
     * @param int $one_way
     */
    public function __doInternalRequest($request, $location, $action, $version, $one_way = 0)
    {
        parent::__doRequest($request, $location, $action, $version, $one_way);
    }

    /**
     * @param string $request
     * @param string $location
     * @param string $action
     * @param int    $version
     * @param int    $one_way
     *
     * @return string
     */
    public function __doRequest($request, $location, $action, $version, $one_way = 0)
    {
        // TODO: Make sure that last request / response is available when the trace option is set!!!
        $request = new SoapRequest($request, $location, $action, $version, $one_way);

        $response = $this->handler->createRequest($request);
        return $response->getResponse();
    }
}
