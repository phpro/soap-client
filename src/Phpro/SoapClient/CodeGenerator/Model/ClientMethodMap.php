<?php

namespace Phpro\SoapClient\CodeGenerator\Model;

use Phpro\SoapClient\Soap\SoapClient;

/**
 * Class ClientMethodMap
 *
 * @package Phpro\SoapClient\CodeGenerator\Model
 */
class ClientMethodMap
{

    /**
     * @var ClientMethod[]
     */
    private $methods = [];

    /**
     * TypeMap constructor.
     *
     * @param array $methods
     * @param string $parameterNamespace
     */
    public function __construct(array $methods, $parameterNamespace = null)
    {
        foreach ($methods as $method) {
            $this->methods[] = new ClientMethod($method, $parameterNamespace);
        }
    }

    /**
     * @param SoapClient $client
     * @param string $parameterNamespace
     * @return ClientMethodMap
     */
    public static function fromSoapClient(SoapClient $client, $parameterNamespace = null)
    {
        return new self($client->__getFunctions(), $parameterNamespace);
    }

    /**
     * @return array
     */
    public function getMethods()
    {
        return $this->methods;
    }
}
