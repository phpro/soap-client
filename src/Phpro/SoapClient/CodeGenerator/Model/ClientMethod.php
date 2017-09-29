<?php

namespace Phpro\SoapClient\CodeGenerator\Model;

use Phpro\SoapClient\CodeGenerator\Parser\FunctionStringParser;

/**
 * Class ClientMethod
 *
 * @package Phpro\SoapClient\CodeGenerator\Model
 */
class ClientMethod
{
    /**
     * @var Parameter[]
     */
    private $parameters;

    /**
     * @var string
     */
    private $methodName;

    /**
     * @var string
     */
    private $returnType;

    /**
     * @var string
     */
    private $parameterNamespace;
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $params;

    /**
     * TypeModel constructor.
     *
     * @param string $name
     * @param array  $params
     * @param string $returnType
     * @param string $parameterNamespace
     */
    public function __construct(string $name, array $params, string $returnType, string $parameterNamespace = '')
    {
        $this->parameterNamespace = $parameterNamespace ?: '';
        $this->methodName = $name;
        $this->parameters = $params;
        $this->returnType = $returnType;
    }

    public static function createFromExtSoapFunctionString(
        string $functionString,
        string $parameterNamespace
    ): ClientMethod {
        $parser = new FunctionStringParser($functionString, $parameterNamespace);

        return new self(
            $parser->parseName(),
            $parser->parseParameters(),
            $parser->parseReturnType(),
            $parameterNamespace
        );
    }


    /**
     * @return array|Parameter[]
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @return string
     */
    public function getMethodName()
    {
        return lcfirst($this->methodName);
    }

    /**
     * @return string
     */
    public function getReturnType()
    {
        return $this->returnType;
    }

    /**
     * @return string
     */
    public function getParameterNamespace()
    {
        return $this->parameterNamespace;
    }
}
