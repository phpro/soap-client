<?php

namespace Phpro\SoapClient\CodeGenerator\Model;

use Phpro\SoapClient\CodeGenerator\Util\Normalizer;
use Soap\Engine\Metadata\Model\Method as MetadataMethod;
use Soap\Engine\Metadata\Model\Parameter as MetadataParameter;
use function Psl\Type\non_empty_string;

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
     * @var non-empty-string
     */
    private $methodName;

    /**
     * @var non-empty-string
     */
    private $returnType;

    /**
     * @var string
     */
    private $parameterNamespace;

    /**
     * TypeModel constructor.
     *
     * @param non-empty-string $name
     * @param array $params
     * @param non-empty-string $returnType
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
            non_empty_string()->assert($method->getName()),
            array_map(
                function (MetadataParameter $parameter) use ($parameterNamespace) {
                    return Parameter::fromMetadata($parameterNamespace, $parameter);
                },
                iterator_to_array($method->getParameters())
            ),
            non_empty_string()->assert($method->getReturnType()->getBaseTypeOrFallbackToName()),
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
     * @return non-empty-string
     */
    public function getMethodName(): string
    {
        return $this->methodName;
    }

    /**
     * @return non-empty-string
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
     * @return non-empty-string
     */
    public function getReturnType(): string
    {
        return Normalizer::normalizeClassname($this->returnType);
    }
}
