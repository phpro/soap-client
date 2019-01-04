<?php

namespace Phpro\SoapClient\CodeGenerator;

use Phpro\SoapClient\ClientBuilder;
use Phpro\SoapClient\ClientFactory;
use Phpro\SoapClient\CodeGenerator\Context\ClientFactoryContext;
use Phpro\SoapClient\CodeGenerator\Model\DuplicateType;
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
\$clientBuilder = new ClientBuilder(\$clientFactory, \$wsdl, %3\$s);
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

        $options = [];
        $soapTypeMapCode = $this->generateSoapTypeMapCode($context->getTypeNamespace(), $context->getDuplicateTypes());
        if ($soapTypeMapCode) {
            $options = [
                'typemap' => $soapTypeMapCode
            ];
        }

        $class->addMethodFromGenerator(
            MethodGenerator::fromArray(
                [
                    'name' => 'factory',
                    'static' => true,
                    'body' => sprintf(
                        self::BODY,
                        $context->getClientName(),
                        $context->getClassmapName(),
                        var_export($options, true)
                    ),
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

    /**
     * @param $typesNamespace
     * @param DuplicateType[]|array $duplicateTypes
     * @return array
     */
    private function generateSoapTypeMapCode($typesNamespace, array $duplicateTypes)
    {
        $typeMaps = [];
        foreach ($duplicateTypes as $duplicateType) {
            $typeMap = [
                'type_name' => $duplicateType->getTypeName(),
                'type_ns' => $duplicateType->getXsdNamespace(),
                'from_xml' =>
                    $typesNamespace.'\\'
                    .$duplicateType->getNamespaceSuffix().'\\'
                    .$duplicateType->getTypeName().'::fromXml'
            ];

            $typeMaps[] = $typeMap;
        }

        return $typeMaps;
    }
}
