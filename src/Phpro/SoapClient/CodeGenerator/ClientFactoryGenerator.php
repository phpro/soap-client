<?php

namespace Phpro\SoapClient\CodeGenerator;

use Phpro\SoapClient\ClientBuilder;
use Phpro\SoapClient\ClientFactory;
use Phpro\SoapClient\CodeGenerator\Context\ClientFactoryContext;
use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\FileGenerator;
use Zend\Code\Generator\MethodGenerator;

/**
 * Class ClientBuilderGenerator
 *
 * @package Phpro\SoapClient\CodeGenerator
 */
class ClientFactoryGenerator implements GeneratorInterface
{
    const BODY = <<<BODY
\$clientFactory = new PhproClientFactory(%1\$s::class);
\$clientBuilder = new ClientBuilder(\$clientFactory, \$wsdl, []);
\$clientBuilder->withClassMaps(%2\$s::getCollection());

return \$clientBuilder->build();

BODY;


    /**
     * @param FileGenerator $file
     * @param ClientFactoryContext $context
     * @return string
     */
    public function generate(FileGenerator $file, $context): string
    {
        $class = new ClassGenerator($context->getClientName().'Factory');
        $class->setNamespaceName($context->getClientNamespace());
        $class->addUse($context->getClientFqcn());
        $class->addUse($context->getClassmapFqcn());
        $class->addUse(ClientFactory::class, 'PhproClientFactory');
        $class->addUse(ClientBuilder::class);
        $class->addMethodFromGenerator(
            MethodGenerator::fromArray(
                [
                    'name' => 'factory',
                    'static' => true,
                    'body' => sprintf(self::BODY, $context->getClientName(), $context->getClassmapName()),
                    'returntype' => $context->getClientFqcn(),
                    'parameters' => [
                        [
                            'name' => 'wsdl',
                            'type' => 'string',
                        ],
                    ],
                ]
            )
        );

        $file->setClass($class);

        return $file->generate();
    }
}
