<?php

namespace Phpro\SoapClient\CodeGenerator\Assembler;

use Phpro\SoapClient\Client;
use Phpro\SoapClient\CodeGenerator\Context\ClientMethodContext;
use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\CodeGenerator\Model\ClientMethod;
use Phpro\SoapClient\CodeGenerator\Util\Normalizer;
use Phpro\SoapClient\Exception\AssemblerException;
use Phpro\SoapClient\Type\MultiArgumentRequest;
use Zend\Code\Generator\MethodGenerator;
use Zend\Code\Generator\ParameterGenerator;

/**
 * Class ClientMethodAssembler
 *
 * @package Phpro\SoapClient\CodeGenerator\Assembler
 */
class ClientMethodAssembler implements AssemblerInterface
{
    /**
     * {@inheritdoc}
     */
    public function canAssemble(ContextInterface $context): bool
    {
        return $context instanceof ClientMethodContext;
    }

    /**
     * @param ContextInterface|ClientMethodContext $context
     *
     * @return bool
     * @throws AssemblerException
     */
    public function assemble(ContextInterface $context): bool
    {
        $class = $context->getClass();
        $class->setExtendedClass(Client::class);
        $method = $context->getMethod();
        try {
            $param = $this->createParamsFromMethod($method);
            $class->removeMethod($method->getMethodName());
            $class->addMethodFromGenerator(
                MethodGenerator::fromArray(
                    [
                        'name'       => $method->getMethodName(),
                        'parameters' => [$param],
                        'visibility' => MethodGenerator::VISIBILITY_PUBLIC,
                        'body'       => sprintf(
                            'return $this->call(\'%s\', $%s);',
                            Normalizer::getClassNameFromFQN($param->getType()),
                            $param->getName()
                        ),
                        'returntype' => $method->getNamespacedReturnType(),
                    ]
                )
            );
        } catch (\Exception $e) {
            throw AssemblerException::fromException($e);
        }

        return true;
    }

    /**
     * Creates the parameters for the record
     *
     * @param ClientMethod $method
     *
     * @return ParameterGenerator
     */
    private function createParamsFromMethod(ClientMethod $method): ParameterGenerator
    {
        $params = $method->getParameters();
        if (count($params) > 1) {
            return ParameterGenerator::fromArray(
                [
                    'name' => 'multiArgumentRequest',
                    'type' => MultiArgumentRequest::class,
                ]
            );
        }
        $param = array_shift($params);

        return ParameterGenerator::fromArray($param->toArray());
    }
}
