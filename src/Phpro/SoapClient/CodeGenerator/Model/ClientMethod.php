<?php

namespace Phpro\SoapClient\CodeGenerator\Model;

use Phpro\SoapClient\CodeGenerator\Context\CodeGeneratorContext;
use Soap\Engine\Metadata\Model\Method as MetadataMethod;
use Soap\Engine\Metadata\Model\MethodMeta;
use Soap\Engine\Metadata\Model\Parameter as MetadataParameter;
use Soap\WsdlReader\Metadata\Predicate\IsConsideredScalarType;
use function Psl\Type\non_empty_string;

final readonly class ClientMethod
{
    /**
     * @internal - Use ClientMethod::fromMetadata instead
     *
     * @param non-empty-string $methodName
     * @param array<array-key, Parameter> $parameters
     */
    public function __construct(
        private string $methodName,
        private array $parameters,
        private ReturnType $returnType,
        private CodeGeneratorContext $codeGeneratorContext,
        private MethodMeta $meta,
    ) {
    }

    public static function fromMetadata(
        CodeGeneratorContext $codeGeneratorContext,
        MetadataMethod $method,
    ): self {
        return new self(
            non_empty_string()->assert($method->getName()),
            array_map(
                function (MetadataParameter $parameter) use ($codeGeneratorContext) {
                    return Parameter::fromMetadata($codeGeneratorContext, $parameter);
                },
                iterator_to_array($method->getParameters())
            ),
            ReturnType::fromMetaData($codeGeneratorContext, $method->getReturnType()),
            $codeGeneratorContext,
            $method->getMeta(),
        );
    }

    public function getCodeGeneratorContext(): CodeGeneratorContext
    {
        return $this->codeGeneratorContext;
    }

    /**
     * @return array<array-key, Parameter>
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
