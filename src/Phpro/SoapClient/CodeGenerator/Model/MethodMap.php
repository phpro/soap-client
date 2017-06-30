<?php

namespace Phpro\SoapClient\CodeGenerator\Model;

use Phpro\SoapClient\CodeGenerator\Util\Normalizer;
use Phpro\SoapClient\Soap\SoapClient;

/**
 * Class MethodMap
 *
 * @package Phpro\SoapClient\CodeGenerator\Model
 */
class MethodMap
{

    /**
     * @var array|
     */
    private $methods = [];

    /**
     * TypeMap constructor.
     *
     * @param array $methods
     */
    public function __construct(array $methods)
    {
        foreach ($methods as $method) {
            $this->methods[] = new Method($method);
        }
    }

    /**
     * @param SoapClient $client
     * @return MethodMap
     */
    public static function fromSoapClient(SoapClient $client)
    {
        return new self($client->__getFunctions());
    }

    /**
     * @return array
     */
    public function getMethods()
    {
        return $this->methods;
    }
}
