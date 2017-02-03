<?php

namespace Phpro\SoapClient\Soap\HttpBinding;

/**
 * Class SoapRequest
 *
 * @package Phpro\SoapClient\Soap\HttpBinding
 */
class SoapRequest
{
    /**
     * @var string
     */
    private $request;

    /**
     * @var string
     */
    private $location;

    /**
     * @var string
     */
    private $action;

    /**
     * @var int
     */
    private $version;

    /**
     * @var int
     */
    private $oneWay;

    /**
     * SoapRequest constructor.
     *
     * @param string $request
     * @param string $location
     * @param string $action
     * @param int    $version
     * @param int    $oneWay
     */
    public function __construct(string $request, string $location, string $action, int $version, int $oneWay = 0)
    {
        $this->request = $request;
        $this->location = $location;
        $this->action = $action;
        $this->version = $version;
        $this->oneWay = $oneWay;
    }

    /**
     * @return string
     */
    public function getRequest(): string
    {
        return $this->request;
    }

    /**
     * @return string
     */
    public function getLocation(): string
    {
        return $this->location;
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @return int
     */
    public function getVersion(): int
    {
        return $this->version;
    }

    /**
     * @return bool
     */
    public function isSOAP11(): bool
    {
        return $this->getVersion() === SOAP_1_1;
    }

    /**
     * @return bool
     */
    public function isSOAP12(): bool
    {
        return $this->getVersion() === SOAP_1_2;
    }

    /**
     * @return int
     */
    public function getOneWay(): int
    {
        return $this->oneWay;
    }
}
