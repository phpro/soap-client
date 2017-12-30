<?php

namespace Phpro\SoapClient\CodeGenerator\Assembler;

use Phpro\SoapClient\Client;
use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\CodeGenerator\Context\ClientMethodContext;
use Phpro\SoapClient\CodeGenerator\Model\Parameter;
use Phpro\SoapClient\Exception\AssemblerException;
use Phpro\SoapClient\Exception\SoapException;
use Phpro\SoapClient\Type\RequestInterface;
use Phpro\SoapClient\Type\ResultInterface;
use Zend\Code\Generator\DocBlockGenerator;
use Zend\Code\Generator\MethodGenerator;

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
                            'return $this->call(\'%1$s\', $%1$s);',
                            $param->getName()
                        ),
                        // TODO: Use normalizer once https://github.com/phpro/soap-client/pull/61 is merged
                        'returntype' => '\\'.$method->getParameterNamespace().'\\'.$method->getReturnType(),
                        'docblock' => DocBlockGenerator::fromArray([
                            'tags' => [
                                [
                                    'name' => 'param',
                                    'description' => sprintf(
                                        '\%s|\%s $%s',
                                        RequestInterface::class,
                                        $param->getType(),
                                        $param->getName()
                                    ),
                                ],
                                [
                                    'name' => 'return',
                                    'description' => '\\' . ResultInterface::class,
                                ],
                                [
                                    'name' => 'throws',
                                    'description' => '\\' . SoapException::class,
                                ],
                            ]
                        ])->setWordWrap(false),
                    ]
                )
            );
        } catch (\Exception $e) {
            throw AssemblerException::fromException($e);
        }

        return true;
    }
}
