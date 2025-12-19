<?php

namespace Phpro\SoapClient\CodeGenerator\Assembler;

use Phpro\SoapClient\CodeGenerator\Context\ClassMapContext;
use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\CodeGenerator\Model\Type;
use Phpro\SoapClient\CodeGenerator\Model\TypeMap;
use Phpro\SoapClient\Exception\AssemblerException;
use Laminas\Code\Generator\ClassGenerator;
use Laminas\Code\Generator\MethodGenerator;
use Soap\Encoding\ClassMap\ClassMap;
use Soap\Encoding\ClassMap\ClassMapCollection;
use Soap\WsdlReader\Metadata\Predicate\IsConsideredScalarType;

/**
 * Class ClassMapAssembler
 *
 * @package Phpro\SoapClient\CodeGenerator\Assembler
 */
class ClassMapAssembler implements AssemblerInterface
{
    /**
     * @param ContextInterface $context
     *
     * @return bool
     */
    public function canAssemble(ContextInterface $context): bool
    {
        return $context instanceof ClassMapContext;
    }

    /**
     * @param ClassMapContext|ContextInterface $context
     *
     * @throws \Phpro\SoapClient\Exception\AssemblerException
     */
    public function assemble(ContextInterface $context)
    {
        $class = new ClassGenerator($context->getName());
        $file = $context->getFile();
        $file->setClass($class);
        $file->setNamespace($context->getNamespace());
        $typeMap = $context->getTypeMap();

        try {
            $file->setUse(ClassMapCollection::class);
            $file->setUse(ClassMap::class);
            $linefeed = $file::LINE_FEED;
            $indentation = $file->getIndentation();

            $class->addMethodFromGenerator($this->generateTypes($typeMap, $linefeed, $indentation));
            $class->addMethodFromGenerator($this->generateEnums($typeMap, $linefeed, $indentation));
        } catch (\Exception $e) {
            throw AssemblerException::fromException($e);
        }
    }

    private function generateTypes(
        TypeMap $typeMap,
        string $linefeed,
        string $indentation,
    ): MethodGenerator {
        $classMap = $this->assembleClassMap(
            $typeMap,
            $linefeed,
            $indentation,
            static fn (Type $type) => !(new IsConsideredScalarType())($type->getMeta())
        );
        $code = $this->assembleClassMapCollection($classMap, $linefeed).$linefeed;

        return (new MethodGenerator('types'))
            ->setStatic(true)
            ->setBody('return '.$code)
            ->setReturnType(ClassMapCollection::class);
    }

    private function generateEnums(
        TypeMap $typeMap,
        string $linefeed,
        string $indentation,
    ): MethodGenerator {
        $classMap = $this->assembleClassMap(
            $typeMap,
            $linefeed,
            $indentation,
            static fn (Type $type) => (new IsConsideredScalarType())($type->getMeta())
                && $type->getMeta()->enums()->isSome()
        );
        $code = $this->assembleClassMapCollection($classMap, $linefeed).$linefeed;

        return (new MethodGenerator('enums'))
            ->setStatic(true)
            ->setBody('return '.$code)
            ->setReturnType(ClassMapCollection::class);
    }

    /**
     * @param \Closure(Type): bool $predicate
     */
    private function assembleClassMap(
        TypeMap $typeMap,
        string $linefeed,
        string $indentation,
        \Closure $predicate
    ): string {
        $classMap = [];
        foreach ($typeMap->getTypes() as $type) {
            if (!$predicate($type)) {
                continue;
            }

            $classMap[] = sprintf(
                '%snew ClassMap(\'%s\', \'%s\', %s::class),',
                $indentation,
                $type->getXsdType()->getXmlNamespace(),
                $type->getXsdType()->getName(),
                '\\' . $type->getFullName(),
            );
        }

        return implode($linefeed, $classMap);
    }

    /**
     * @param string $classMap
     * @param string $linefeed
     *
     * @return string
     */
    private function assembleClassMapCollection(string $classMap, string $linefeed): string
    {
        $code = [
            'new ClassMapCollection(',
            '%s',
            ');',
        ];

        return sprintf(implode($linefeed, $code), $classMap);
    }
}
