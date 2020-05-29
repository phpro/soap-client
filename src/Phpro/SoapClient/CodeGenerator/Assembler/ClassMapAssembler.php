<?php

namespace Phpro\SoapClient\CodeGenerator\Assembler;

use Phpro\SoapClient\CodeGenerator\Context\ClassMapContext;
use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\CodeGenerator\Model\TypeMap;
use Phpro\SoapClient\Exception\AssemblerException;
use Phpro\SoapClient\Soap\ClassMap\ClassMap;
use Phpro\SoapClient\Soap\ClassMap\ClassMapCollection;
use Laminas\Code\Generator\ClassGenerator;
use Laminas\Code\Generator\MethodGenerator;

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
        $class = ClassGenerator::fromArray(
            [
                'name' => $context->getName(),
            ]
        );
        $file = $context->getFile();
        $file->setClass($class);
        $file->setNamespace($context->getNamespace());
        $typeMap = $context->getTypeMap();
        $typeNamespace = $typeMap->getNamespace();
        $file->setUse($typeNamespace, preg_match('/\\\\Type$/', $typeNamespace) ? null : 'Type');

        try {
            $file->setUse(ClassMapCollection::class);
            $file->setUse(ClassMap::class);
            $linefeed = $file::LINE_FEED;
            $classMap = $this->assembleClassMap($typeMap, $linefeed, $file->getIndentation());
            $code = $this->assembleClassMapCollection($classMap, $linefeed).$linefeed;
            $class->addMethodFromGenerator(
                MethodGenerator::fromArray(
                    [
                        'name'       => 'getCollection',
                        'static'     => true,
                        'body'       => 'return '.$code,
                        'returntype' => ClassMapCollection::class,
                    ]
                )
            );
        } catch (\Exception $e) {
            throw AssemblerException::fromException($e);
        }
    }

    /***
     * @param TypeMap $typeMap
     * @param string  $linefeed
     * @param string  $indentation
     *
     * @return string
     */
    private function assembleClassMap(TypeMap $typeMap, string $linefeed, string $indentation): string
    {
        $classMap = [];
        foreach ($typeMap->getTypes() as $type) {
            $classMap[] = sprintf(
                '%snew ClassMap(\'%s\', %s::class),',
                $indentation,
                $type->getXsdName(),
                'Type\\'.$type->getName()
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
            'new ClassMapCollection([',
            '%s',
            ']);',
        ];

        return sprintf(implode($linefeed, $code), $classMap);
    }
}
