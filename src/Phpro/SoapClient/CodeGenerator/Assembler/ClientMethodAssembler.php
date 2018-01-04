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
use Zend\Code\Generator\ClassGenerator;
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
                                        '%s|%s $%s',
                                        $this->generateShortClassNameAndAddImport(RequestInterface::class, $class),
                                        $this->generateShortClassNameAndAddImport($param->getType(), $class),
                                        $param->getName()
                                    ),
                                ],
                                [
                                    'name' => 'return',
                                    'description' => sprintf(
                                        '%s|%s',
                                        $this->generateShortClassNameAndAddImport(ResultInterface::class, $class),
                                        $this->generateShortClassNameAndAddImport(
                                            $method->getParameterNamespace().'\\'.$method->getReturnType(),
                                            $class
                                        )
                                    ),

                                ],
                                [
                                    'name' => 'throws',
                                    'description' => $this->generateShortClassNameAndAddImport(
                                        SoapException::class,
                                        $class
                                    ),
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

    /**
     * @param string         $fqnClassName   Fully qualified class name.
     * @param ClassGenerator $classGenerator Class generator object.
     *
     * @return string
     */
    protected function generateShortClassNameAndAddImport(string $fqnClassName, ClassGenerator $classGenerator): string
    {
        $fqnClassName = ltrim($fqnClassName, '\\');
        $parts = explode('\\', $fqnClassName);
        $className = array_pop($parts);
        $classNamespace = implode('\\', $parts);
        $currentNamespace = (string) $classGenerator->getNamespaceName();

        if ($classNamespace !== $currentNamespace || ! in_array($fqnClassName, $classGenerator->getUses())) {
            $classGenerator->addUse($fqnClassName);
        }

        return $className;
    }
}
