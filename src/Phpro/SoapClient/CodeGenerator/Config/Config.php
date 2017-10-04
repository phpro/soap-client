<?php

namespace Phpro\SoapClient\CodeGenerator\Config;

use Phpro\SoapClient\CodeGenerator\Assembler;
use Phpro\SoapClient\CodeGenerator\Rules;
use Phpro\SoapClient\CodeGenerator\Rules\RuleInterface;
use Phpro\SoapClient\CodeGenerator\Rules\RuleSet;
use Phpro\SoapClient\CodeGenerator\Rules\RuleSetInterface;
use Phpro\SoapClient\CodeGenerator\Util\Normalizer;
use Phpro\SoapClient\Exception\InvalidArgumentException;
use Phpro\SoapClient\Exception\WsdlException;
use Phpro\SoapClient\Wsdl\Provider\MixedWsdlProvider;
use Phpro\SoapClient\Wsdl\Provider\WsdlProviderInterface;

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
     * @var array
     */
    protected $soapOptions = [
        'trace' => false,
        'exceptions' => true,
        'keep_alive' => true,
        'cache_wsdl' => WSDL_CACHE_NONE,
    ];

    /**
     * @var string
     */
    protected $destination = '';

    /**
     * @var RuleSetInterface
     */
    protected $ruleSet;

    /**
     * @var WsdlProviderInterface
     */
    protected $wsdlProvider;

    /**
     * Config constructor.
     *
     * @param string $wsdl
     * @param string $destination
     */
    public function __construct(string $wsdl = '', string $destination = '')
    {
        $this->setWsdl($wsdl);
        $this->setDestination($destination);
        $this->ruleSet = new RuleSet([
            new Rules\AssembleRule(new Assembler\PropertyAssembler()),
            new Rules\AssembleRule(new Assembler\ClassMapAssembler()),
        ]);
        $this->wsdlProvider = new MixedWsdlProvider();
    }

    /**
     * @return Config
     */
    public static function create(): self
    {
        return new static();
    }

    /**
     * @return string
     * @throws InvalidArgumentException
     */
    public function getNamespace(): string
    {
        return $this->namespace;
    }

    /**
     * @param string $namespace
     *
     * @return Config
     */
    public function setNamespace(string $namespace): self
    {
        $this->namespace = Normalizer::normalizeNamespace($namespace);
        return $this;
    }

    /**
     * @return string
     * @throws InvalidArgumentException
     */
    public function getWsdl(): string
    {
        if (!$this->wsdl) {
            throw InvalidArgumentException::wsdlConfigurationIsMissing();
        }

        try {
            $wsdl = $this->wsdlProvider->provide($this->wsdl);
        } catch (WsdlException $e) {
            throw InvalidArgumentException::wsdlCouldNotBeProvided($e);
        }

        return $wsdl;
    }

    /**
     * @param array $soapOptions
     *
     * @return $this
     */
    public function setSoapOptions(array $soapOptions): self
    {
        $this->soapOptions = $soapOptions;

        return $this;
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function addSoapOption(string $key, $value): self
    {
        $this->soapOptions[$key] = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function getSoapOptions(): array
    {
        return $this->soapOptions;
    }

    /**
     * @param string $wsdl
     *
     * @return Config
     */
    public function setWsdl(string $wsdl): self
    {
        $this->wsdl = $wsdl;
        return $this;
    }

    /**
     * @return string
     * @throws InvalidArgumentException
     */
    public function getDestination(): string
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
    public function setDestination(string $destination): self
    {
        $this->destination = rtrim($destination, '/\\');
        return $this;
    }

    /**
     * @return RuleSetInterface
     */
    public function getRuleSet(): RuleSetInterface
    {
        return $this->ruleSet;
    }

    /**
     * @param RuleInterface $rule
     *
     * @return Config
     */
    public function addRule(RuleInterface $rule): self
    {
        $this->ruleSet->addRule($rule);
        return $this;
    }

    /**
     * @param RuleSetInterface $ruleSet
     *
     * @return Config
     */
    public function setRuleSet(RuleSetInterface $ruleSet): self
    {
        $this->ruleSet = $ruleSet;
        return $this;
    }

    /**
     * @return WsdlProviderInterface
     */
    public function getWsdlProvider(): WsdlProviderInterface
    {
        return $this->wsdlProvider;
    }

    /**
     * @param WsdlProviderInterface $wsdlProvider
     * @return Config
     */
    public function setWsdlProvider(WsdlProviderInterface $wsdlProvider): self
    {
        $this->wsdlProvider = $wsdlProvider;
        return $this;
    }
}
