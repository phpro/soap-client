<?php

namespace Phpro\SoapClient\CodeGenerator\Model;

use Phpro\SoapClient\CodeGenerator\Context\CodeGeneratorContext;
use Phpro\SoapClient\CodeGenerator\TypeEnhancer\Calculator\TypeNameCalculator;
use Phpro\SoapClient\CodeGenerator\Util\Normalizer;
use Soap\Engine\Metadata\Model\Parameter as MetadataParameter;
use Soap\Engine\Metadata\Model\TypeMeta;
use Soap\Engine\Metadata\Model\XsdType;
use function Psl\Type\non_empty_string;

final readonly class Parameter
{
    private TypeMeta $meta;

    /**
     * @internal - Use Parameter::fromMetadata instead
     *
     * Parameter constructor.
     *
     * @param non-empty-string $name
     * @param non-empty-string $type
     */
    public function __construct(
        private string $name,
        private string $type,
        private CodeGeneratorContext $codeGeneratorContext,
        private XsdType $xsdType,
    ) {
        $this->meta = $xsdType->getMeta();
    }

    public static function fromMetadata(
        CodeGeneratorContext $codeGeneratorContext,
        MetadataParameter $parameter,
    ): Parameter {
        $type = $parameter->getType();
        $typeName = (new TypeNameCalculator())($type);

        return new self(
            Normalizer::normalizeProperty(non_empty_string()->assert($parameter->getName())),
            Normalizer::normalizeDataType(non_empty_string()->assert($typeName)),
            $codeGeneratorContext,
            $type,
        );
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return non-empty-string
     */
    public function getType(): string
    {
        if (Normalizer::isKnownType($this->type)) {
            return $this->type;
        }

        $normalized = $this->codeGeneratorContext->codingStandards->normalizeTypeName($this->type);

        return '\\'.$this->getNamespace().'\\'.$normalized;
    }

    /**
     * @return non-empty-string
     */
    public function getNamespace(): string
    {
        return $this->codeGeneratorContext->typeNamespaceMap->detectDestinationForType($this->xsdType)->namespace;
    }

    public function getXsdType(): XsdType
    {
        return $this->xsdType;
    }

    public function getMeta(): TypeMeta
    {
        return $this->meta;
    }
}
