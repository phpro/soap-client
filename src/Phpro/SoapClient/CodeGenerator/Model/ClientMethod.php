<?php

namespace Phpro\SoapClient\CodeGenerator\Model;

use Phpro\SoapClient\CodeGenerator\Parser\FunctionStringParser;
use Phpro\SoapClient\CodeGenerator\Util\Normalizer;

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
     * TypeModel constructor.
     *
     * @param string $name
     * @param array $params
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

    /**
     * Creates an instance from parsing a soap function string
     *
     * @param string $functionString
     * @param string $parameterNamespace
     *
     * @return ClientMethod
     */
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
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @return string
     */
    public function getMethodName(): string
    {
        return lcfirst($this->methodName);
    }

    /**
     * @return string
     */
    public function getNamespacedReturnType(): string
    {
        return '\\'.Normalizer::normalizeNamespace($this->getParameterNamespace().'\\'.$this->getReturnType());
    }

    /**
     * @return string
     */
    public function getParameterNamespace(): string
    {
        return $this->parameterNamespace;
    }

    /**
     * @return string
     */
    public function getReturnType(): string
    {
        return $this->returnType;
    }
}
