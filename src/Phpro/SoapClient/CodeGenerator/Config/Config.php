<?php

namespace Phpro\SoapClient\CodeGenerator\Config;

use Phpro\SoapClient\CodeGenerator\Assembler;
use Phpro\SoapClient\CodeGenerator\Rules;
use Phpro\SoapClient\CodeGenerator\Rules\RuleInterface;
use Phpro\SoapClient\CodeGenerator\Rules\RuleSet;
use Phpro\SoapClient\CodeGenerator\Rules\RuleSetInterface;
use Phpro\SoapClient\CodeGenerator\Util\Normalizer;
use Phpro\SoapClient\Exception\InvalidArgumentException;

/**
 * Class Config
 *
 * @package Phpro\SoapClient\CodeGenerator\Config
 */
class Config implements ConfigInterface
{

    /**
     * @var string
     */
    protected $namespace = '';

    /**
     * @var string
     */
    protected $wsdl = '';

    /**
     * @var string
     */
    protected $destination = '';

    /**
     * @var RuleSetInterface
     */
    protected $ruleSet;

    /**
     * Config constructor.
     *
     * @param string $wsdl
     * @param string $destination
     */
    public function __construct($wsdl = '', $destination = '')
    {
        $this->setWsdl($wsdl);
        $this->setDestination($destination);
        $this->ruleSet = new RuleSet([
            new Rules\AssembleRule(new Assembler\PropertyAssembler()),
            new Rules\AssembleRule(new Assembler\ClassMapAssembler()),
        ]);
    }

    /**
     * @return Config
     */
    public static function create()
    {
        return new static();
    }

    /**
     * @return string
     * @throws InvalidArgumentException
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @param string $namespace
     *
     * @return Config
     */
    public function setNamespace($namespace)
    {
        $this->namespace = Normalizer::normalizeNamespace($namespace);
        return $this;
    }

    /**
     * @return string
     * @throws InvalidArgumentException
     */
    public function getWsdl()
    {
        if (!$this->wsdl) {
            throw InvalidArgumentException::wsdlConfigurationIsMissing();
        }

        return $this->wsdl;
    }

    /**
     * @param string $wsdl
     *
     * @return Config
     */
    public function setWsdl($wsdl)
    {
        $this->wsdl = $wsdl;
        return $this;
    }

    /**
     * @return string
     * @throws InvalidArgumentException
     */
    public function getDestination()
    {
        if (!$this->destination) {
            throw InvalidArgumentException::destinationConfigurationIsMissing();
        }

        return $this->destination;
    }

    /**
     * @param string $destination
     *
     * @return Config
     */
    public function setDestination($destination)
    {
        $this->destination = rtrim($destination, '/\\');
        return $this;
    }

    /**
     * @return RuleSetInterface
     */
    public function getRuleSet()
    {
        return $this->ruleSet;
    }

    /**
     * @param RuleInterface $rule
     *
     * @return Config
     */
    public function addRule(RuleInterface $rule)
    {
        $this->ruleSet->addRule($rule);
        return $this;
    }

    /**
     * @param RuleSetInterface $ruleSet
     *
     * @return Config
     */
    public function setRuleSet(RuleSetInterface $ruleSet)
    {
        $this->ruleSet = $ruleSet;
        return $this;
    }
}
