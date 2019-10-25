<?php

namespace Phpro\SoapClient\CodeGenerator;

use Phpro\SoapClient\CodeGenerator\Context\ClientMethodContext;
use Phpro\SoapClient\CodeGenerator\Context\PropertyContext;
use Phpro\SoapClient\CodeGenerator\Context\TypeContext;
use Phpro\SoapClient\CodeGenerator\Model\Client;
use Phpro\SoapClient\CodeGenerator\Model\Type;
use Phpro\SoapClient\CodeGenerator\Rules\RuleSetInterface;
use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\FileGenerator;

/**
 * Class ClientGenerator
 *
 * @package Phpro\SoapClient\CodeGenerator
 */
class ClientGenerator implements GeneratorInterface
{
    /**
     * @var RuleSetInterface
     */
    private $ruleSet;

    /**
     * TypeGenerator constructor.
     *
     * @param RuleSetInterface $ruleSet
     */
    public function __construct(RuleSetInterface $ruleSet)
    {
        $this->ruleSet = $ruleSet;
    }

    /**
     * @param FileGenerator $file
     * @param Client        $client
     *
     * @return string
     */
    public function generate(FileGenerator $file, $client): string
    {
        $class = $file->getClass() ?: new ClassGenerator();
        $class->setNamespaceName($client->getNamespace());
        $class->setName($client->getName());
        $methods = $client->getMethodMap();

        foreach ($methods->getMethods() as $method) {
            $this->ruleSet->applyRules(new ClientMethodContext($class, $method));
        }

        $file->setClass($class);

        return $file->generate();
    }
}
