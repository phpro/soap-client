<?php

namespace Phpro\SoapClient\CodeGenerator\Config;

use Closure;
use Phpro\SoapClient\CodeGenerator\Assembler;
use Phpro\SoapClient\CodeGenerator\Rules;
use Phpro\SoapClient\CodeGenerator\Rules\RuleInterface;
use Phpro\SoapClient\CodeGenerator\Rules\RuleSet;
use Phpro\SoapClient\CodeGenerator\Rules\RuleSetInterface;
use Phpro\SoapClient\Exception\InvalidArgumentException;
use Phpro\SoapClient\Soap\Metadata\Detector\LocalEnumDetector;
use Phpro\SoapClient\Soap\Metadata\Manipulators\DuplicateTypes\IntersectDuplicateTypesStrategy;
use Phpro\SoapClient\Soap\Metadata\Manipulators\MethodsManipulatorChain;
use Phpro\SoapClient\Soap\Metadata\Manipulators\TypeReplacer\LocalToGlobalEnumReplacer;
use Phpro\SoapClient\Soap\Metadata\Manipulators\TypeReplacer\AppendTypesManipulator;
use Phpro\SoapClient\Soap\Metadata\Manipulators\TypeReplacer\ReplaceMethodTypesManipulator;
use Phpro\SoapClient\Soap\Metadata\Manipulators\TypeReplacer\ReplaceTypesManipulator;
use Phpro\SoapClient\Soap\Metadata\Manipulators\TypeReplacer\TypeReplacer;
use Phpro\SoapClient\Soap\Metadata\Manipulators\TypeReplacer\TypeReplacers;
use Phpro\SoapClient\Soap\Metadata\Manipulators\TypesManipulatorChain;
use Phpro\SoapClient\Soap\Metadata\Manipulators\TypesManipulatorInterface;
use Phpro\SoapClient\Soap\Metadata\MetadataFactory;
use Phpro\SoapClient\Soap\Metadata\MetadataOptions;
use Soap\Engine\Engine;
use Soap\Engine\Metadata\Collection\TypeCollection;
use Soap\Engine\Metadata\Metadata;

final class Config
{
    protected ?ClientConfig $client = null;

    protected ?TypeNamespaceMap $typeNamespaceMap = null;
    protected ?Engine $engine = null;

    /**
     * @var TypesManipulatorInterface|Closure(?TypeNamespaceMap): TypesManipulatorInterface
     */
    protected TypesManipulatorInterface|Closure $duplicateTypeIntersectStrategy;

    protected TypeReplacer $typeReplacementStrategy;

    protected ?MetadataOptions $metadataOptions = null;

    protected RuleSetInterface $ruleSet;

    protected ?ClassMapConfig $classMap = null;
    protected EnumerationGenerationStrategy $enumerationGenerationStrategy;

    public function __construct()
    {
        $this->typeReplacementStrategy = TypeReplacers::defaults();

        // Working with duplicate types is hard (see FAQ).
        // Therefore, we decided to combine all duplicate types into 1 big intersected type by default instead.
        // The resulting type will always be usable, but might contain some additional empty properties.
        $this->duplicateTypeIntersectStrategy = IntersectDuplicateTypesStrategy::create();

        // By default, we only generate global enumerations to avoid naming conflicts.
        $this->enumerationGenerationStrategy = EnumerationGenerationStrategy::default();

        $this->ruleSet = new RuleSet([
            new Rules\AssembleRule(new Assembler\PropertyAssembler()),
            new Rules\AssembleRule(new Assembler\ClassMapAssembler()),
            new Rules\AssembleRule(new Assembler\ClientConstructorAssembler()),
            new Rules\AssembleRule(new Assembler\ClientMethodAssembler())
        ]);
    }

    public static function create(): self
    {
        return new static();
    }

    public function getTypeNamespaceMap(): TypeNamespaceMap
    {
        if (!$this->typeNamespaceMap) {
            throw InvalidArgumentException::typeNamespaceMapIsMissing();
        }

        return $this->typeNamespaceMap;
    }

    public function setTypeNamespaceMap(TypeNamespaceMap $namespaceMap): self
    {
        $this->typeNamespaceMap = $namespaceMap;

        return $this;
    }

    public function getEngine(): Engine
    {
        if (!$this->engine instanceof Engine) {
            throw InvalidArgumentException::engineNotConfigured();
        }
        return $this->engine;
    }

    public function setEngine(Engine $engine): self
    {
        $this->engine = $engine;

        return $this;
    }

    public function getRuleSet(): RuleSetInterface
    {
        return $this->ruleSet;
    }

    public function setRuleSet(RuleSetInterface $ruleSet): self
    {
        $this->ruleSet = $ruleSet;

        return $this;
    }

    public function addRule(RuleInterface $rule): self
    {
        $this->ruleSet->addRule($rule);

        return $this;
    }

    public function getClient(): ClientConfig
    {
        if (!$this->client) {
            throw InvalidArgumentException::clientIsMissing();
        }

        return $this->client;
    }

    public function setClient(ClientConfig $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getMetadataOptions(): MetadataOptions
    {
        if ($this->metadataOptions) {
            return $this->metadataOptions;
        }

        $typeReplacementStrategy = $this->typeReplacementStrategy;
        $appendTypes = static fn () => new TypeCollection();

        if ($this->enumerationGenerationStrategy === EnumerationGenerationStrategy::LocalAndGlobal) {
            $typeReplacementStrategy = new TypeReplacers($typeReplacementStrategy, new LocalToGlobalEnumReplacer());
            $appendTypes = new LocalEnumDetector();
        }

        return MetadataOptions::empty()
            ->withTypesManipulator(
                new TypesManipulatorChain(
                    new AppendTypesManipulator($appendTypes),
                    $this->resolveDuplicateTypeStrategy(),
                    new ReplaceTypesManipulator($typeReplacementStrategy),
                )
            )->withMethodsManipulator(
                new MethodsManipulatorChain(
                    new ReplaceMethodTypesManipulator($typeReplacementStrategy)
                )
            );
    }

    public function getManipulatedMetadata(): Metadata
    {
        return MetadataFactory::manipulated(
            $this->getEngine()->getMetadata(),
            $this->getMetadataOptions()
        );
    }

    public function setTypeReplacementStrategy(TypeReplacer $typeReplacementStrategy): self
    {
        $this->typeReplacementStrategy = $typeReplacementStrategy;

        return $this;
    }

    /**
     * @param TypesManipulatorInterface|Closure(?TypeNamespaceMap): TypesManipulatorInterface $duplicateTypeIntersectStrategy
     */
    public function setDuplicateTypeIntersectStrategy(TypesManipulatorInterface|Closure $duplicateTypeIntersectStrategy): self
    {
        $this->duplicateTypeIntersectStrategy = $duplicateTypeIntersectStrategy;

        return $this;
    }

    public function setMetadataOptions(MetadataOptions $metadataOptions): self
    {
        $this->metadataOptions = $metadataOptions;

        return $this;
    }

    public function getClassMap(): ClassMapConfig
    {
        if (!$this->classMap) {
            throw InvalidArgumentException::classmapMissing();
        }

        return $this->classMap;
    }

    public function setClassMap(ClassMapConfig $classMap): self
    {
        $this->classMap = $classMap;

        return $this;
    }

    public function setEnumerationGenerationStrategy(EnumerationGenerationStrategy $enumerationGenerationStrategy): self
    {
        $this->enumerationGenerationStrategy = $enumerationGenerationStrategy;

        return $this;
    }

    public function getEnumerationGenerationStrategy(): EnumerationGenerationStrategy
    {
        return $this->enumerationGenerationStrategy;
    }

    private function resolveDuplicateTypeStrategy(): TypesManipulatorInterface
    {
        if ($this->duplicateTypeIntersectStrategy instanceof Closure) {
            return ($this->duplicateTypeIntersectStrategy)($this->typeNamespaceMap);
        }

        return $this->duplicateTypeIntersectStrategy;
    }
}
