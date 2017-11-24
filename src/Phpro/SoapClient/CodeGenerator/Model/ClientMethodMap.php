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
    private $methods;

    public function __construct(array $methods)
    {
        $this->methods = $methods;
    }

    public static function fromSoapClient(SoapClient $client, $parameterNamespace = '') : ClientMethodMap
    {
        $clientMethods = [];
        foreach ($client->__getFunctions() as $method) {
            $clientMethods[] = ClientMethod::createFromExtSoapFunctionString($method, $parameterNamespace);
        }

        return new self($clientMethods);
    }

    public function getMethods() : array
    {
        return $this->methods;
    }
}
