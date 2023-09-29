<?php

namespace Phpro\SoapClient\CodeGenerator\Model;

use Soap\Engine\Metadata\Model\Method as MetadataMethod;
use Soap\Engine\Metadata\Model\MethodMeta;
use Soap\Engine\Metadata\Model\Parameter as MetadataParameter;
use Soap\WsdlReader\Metadata\Predicate\IsConsideredScalarType;
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
    private array $parameters;

    /**
     * @var non-empty-string
     */
    private string $methodName;

    private ReturnType $returnType;

    /**
     * @var string
     */
    private string $parameterNamespace;

    private MethodMeta $meta;

    /**
     * TypeModel constructor.
     *
     * @param non-empty-string $name
     * @param array $params
     * @param string $parameterNamespace
     */
    public function __construct(
        string $name,
        array $params,
        ReturnType $returnType,
        string $parameterNamespace,
        MethodMeta $meta
    ) {
        $this->parameterNamespace = $parameterNamespace;
        $this->methodName = $name;
        $this->parameters = $params;
        $this->returnType = $returnType;
        $this->meta = $meta;
    }

    /**
     * @param non-empty-string $parameterNamespace
     */
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
            ReturnType::fromMetaData($parameterNamespace, $method->getReturnType()),
            $parameterNamespace,
            $method->getMeta()
        );
    }

    /**
     * @return array|Parameter[]
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function getParametersCount(): int
    {
        return count($this->parameters);
    }

    /**
     * @return non-empty-string
     */
    public function getMethodName(): string
    {
        return $this->methodName;
    }

    /**
     * @return string
     */
    public function getParameterNamespace(): string
    {
        return $this->parameterNamespace;
    }

    public function getReturnType(): ReturnType
    {
        return $this->returnType;
    }

    public function getMeta(): MethodMeta
    {
        return $this->meta;
    }

    public function shouldGenerateAsMultiArgumentsRequest(): bool
    {
        $paramCount = $this->getParametersCount();
        if ($paramCount > 1) {
            return true;
        }

        if ($paramCount === 1) {
            $param = current($this->getParameters());

            return (new IsConsideredScalarType())($param->getMeta());
        }

        return false;
    }
}
