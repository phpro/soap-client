<?php

namespace Phpro\SoapClient\CodeGenerator\Assembler;

use Phpro\SoapClient\Client;
use Phpro\SoapClient\CodeGenerator\Assembler\AssemblerInterface;
use Phpro\SoapClient\CodeGenerator\Context\ClientContext;
use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\CodeGenerator\Context\MethodContext;
use Phpro\SoapClient\CodeGenerator\Context\PropertyContext;
use Phpro\SoapClient\CodeGenerator\Model\Parameter;
use Phpro\SoapClient\CodeGenerator\Util\Normalizer;
use Phpro\SoapClient\Exception\AssemblerException;
use Zend\Code\Generator\DocBlockGenerator;
use Zend\Code\Generator\MethodGenerator;

/**
 * Class MethodAssembler
 *
 * @package Phpro\SoapClient\CodeGenerator\Assembler
 */
class MethodAssembler implements AssemblerInterface
{
    /**
     * {@inheritdoc}
     */
    public function canAssemble(ContextInterface $context)
    {
        return $context instanceof MethodContext;
    }

    /**
     * @param ContextInterface|MethodContext $context
     *
     * @throws AssemblerException
     */
    public function assemble(ContextInterface $context)
    {
        $class = $context->getClass();
        $class->setExtendedClass(Client::class);
        $method = $context->getMethod();
        try {
            $params = $method->getParameters();
            /** @var Parameter $param */
            $param = array_shift($params);
            $class->removeMethod($method->getMethodName());
            $class->addMethodFromGenerator(
                MethodGenerator::fromArray(
                    [
                        'name'       => $method->getMethodName(),
                        'parameters' => $method->getParameters(),
                        'visibility' => MethodGenerator::VISIBILITY_PUBLIC,
                        'body'       => sprintf(
                            'return $this->call(\'%1$s\', $%2$s);',
                            $param->getOriginalType(),
                            $param->getName()
                        ),
                        'docblock'   => DocBlockGenerator::fromArray(
                            [
                                'tags' => [
                                    [
                                        'name'        => 'return',
                                        'description' => $method->getReturnType(),
                                    ],
                                ],
                            ]
                        ),
                    ]
                )
            );
        } catch (\Exception $e) {
            throw AssemblerException::fromException($e);
        }
    }
}
