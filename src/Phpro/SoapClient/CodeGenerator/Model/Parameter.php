<?php

namespace Phpro\SoapClient\CodeGenerator\Model;

use Phpro\SoapClient\CodeGenerator\Config\TypeNamespaceMap;
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
        private TypeNamespaceMap $namespaces,
        private XsdType $xsdType
    ) {
        $this->meta = $xsdType->getMeta();
    }

    public static function fromMetadata(TypeNamespaceMap $typeNamespaceMap, MetadataParameter $parameter): Parameter
    {
        $type = $parameter->getType();
        $typeName = (new TypeNameCalculator())($type);

        return new self(
            Normalizer::normalizeProperty(non_empty_string()->assert($parameter->getName())),
            Normalizer::normalizeDataType(non_empty_string()->assert($typeName)),
            $typeNamespaceMap,
            $type
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

        return '\\'.$this->getNamespace().'\\'.Normalizer::normalizeClassname($this->type);
    }

    /**
     * @return non-empty-string
     */
    public function getNamespace(): string
    {
        return $this->namespaces->detectDestinationForType($this->xsdType)->namespace;
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
