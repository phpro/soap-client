<?php

namespace Phpro\SoapClient\CodeGenerator\Model;

use Phpro\SoapClient\CodeGenerator\Util\Normalizer;
use Phpro\SoapClient\Soap\Engine\Metadata\Model\Method as MetadataMethod;
use Phpro\SoapClient\Soap\Engine\Metadata\Model\Parameter as MetadataParameter;

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

    public static function fromMetadata(
        string $parameterNamespace,
        MetadataMethod $method
    ): self {
        return new self(
            $method->getName(),
            array_map(
                function (MetadataParameter $parameter) use ($parameterNamespace) {
                    return Parameter::fromMetadata($parameterNamespace, $parameter);
                },
                $method->getParameters()
            ),
            $method->getReturnType()->getBaseTypeOrFallbackToName(),
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
        return $this->methodName;
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
        return Normalizer::normalizeClassname($this->returnType);
    }
}
