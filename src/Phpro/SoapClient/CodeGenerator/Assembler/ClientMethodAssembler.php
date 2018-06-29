<?php

namespace Phpro\SoapClient\CodeGenerator\Assembler;

use Phpro\SoapClient\Client;
use Phpro\SoapClient\CodeGenerator\Context\ClientMethodContext;
use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\CodeGenerator\Util\Normalizer;
use Phpro\SoapClient\Exception\AssemblerException;
use Phpro\SoapClient\Type\MultiArgumentRequest;
use Zend\Code\Generator\DocBlockGenerator;
use Zend\Code\Generator\MethodGenerator;
use Zend\Code\Generator\ParameterGenerator;

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
            $param = $this->createParamsFromContext($context);
            $class->removeMethod($method->getMethodName());
            $methodGeneratorConfig = [
                'name' => $method->getMethodName(),
                'parameters' => [$param],
                'visibility' => MethodGenerator::VISIBILITY_PUBLIC,
                'body' => sprintf(
                    'return $this->call(\'%s\', $%s);',
                    Normalizer::getClassNameFromFQN($param->getType()),
                    $param->getName()
                ),
                'returntype' => $method->getNamespacedReturnType(),
            ];
            if ($context->isMultiArgument()) {
                $methodGeneratorConfig['docblock'] = $this->generateMultiArgumentDocblock($context);
            }

            $class->addMethodFromGenerator(MethodGenerator::fromArray($methodGeneratorConfig));
        } catch (\Exception $e) {
            throw AssemblerException::fromException($e);
        }

        return true;
    }

    /**
     * @param ClientMethodContext $context
     *
     * @return ParameterGenerator
     */
    private function createParamsFromContext(ClientMethodContext $context): ParameterGenerator
    {
        if (!$context->isMultiArgument()) {
            $param = current($context->getMethod()->getParameters());

            return ParameterGenerator::fromArray($param->toArray());
        }

        return ParameterGenerator::fromArray(
            [
                'name' => 'multiArgumentRequest',
                'type' => MultiArgumentRequest::class,
            ]
        );
    }

    /**
     * @param ClientMethodContext $context
     *
     * @return DocBlockGenerator
     */
    private function generateMultiArgumentDocblock(ClientMethodContext $context): DocBlockGenerator
    {
        $description = ['MultiArgumentRequest with following params:'.PHP_EOL];
        foreach ($context->getMethod()->getParameters() as $parameter) {
            $description[] = $parameter->getType().' $'.$parameter->getName();
        }

        return DocBlockGenerator::fromArray(
            [
                'longdescription' => implode(PHP_EOL, $description),
                'tags' => [
                    ['name' => 'param', 'description' => MultiArgumentRequest::class],
                    ['name' => 'return', 'description' => $context->getMethod()->getReturnType()],
                ],
            ]
        );
    }
}
