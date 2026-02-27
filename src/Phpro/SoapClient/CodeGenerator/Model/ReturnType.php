<?php
declare(strict_types=1);

namespace Phpro\SoapClient\CodeGenerator\Model;

use Phpro\SoapClient\CodeGenerator\Config\TypeNamespaceMap;
use Phpro\SoapClient\CodeGenerator\TypeEnhancer\Calculator\TypeNameCalculator;
use Phpro\SoapClient\CodeGenerator\Util\Normalizer;
use Soap\Engine\Metadata\Model\TypeMeta;
use Soap\Engine\Metadata\Model\XsdType;
use Soap\WsdlReader\Metadata\Predicate\IsConsideredScalarType;
use function Psl\Type\non_empty_string;

final readonly class ReturnType
{
    private TypeMeta $meta;

    /**
     * @internal - Use ReturnType::fromMetaData instead
     *
     * @param non-empty-string $type
     */
    public function __construct(
        private string $type,
        private TypeNamespaceMap $namespaces,
        private XsdType $xsdType
    ) {
        $this->meta = $xsdType->getMeta();
    }

    public static function fromMetaData(TypeNamespaceMap $namespaces, XsdType $returnType): self
    {
        // Element types that are referencing complex types, should result in the complexType according to ext-soap:
        $returnType = $returnType->copy($returnType->getXmlTypeName() ?: $returnType->getName());

        $typeName = (new TypeNameCalculator())($returnType);

        return new self(
            Normalizer::normalizeDataType(non_empty_string()->assert($typeName)),
            $namespaces,
            $returnType
        );
    }
    /**
     * @return non-empty-string
     */
    public function getType(): string
    {
        if (Normalizer::isKnownType($this->type)) {
            return $this->type;
        }
        
        if ($this->meta->isSimple()->unwrapOr(false)) {
            return $this->meta->extends()
                ->filter(static fn (array $extends): bool => $extends['isSimple'])
                ->map(static fn (array $extends): string => $extends['type'])
                ->unwrapOr('mixed');
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

    public function shouldGenerateAsMixedResult(): bool
    {
        return (new IsConsideredScalarType())($this->meta)
            || Normalizer::isKnownType($this->type);
    }
}
