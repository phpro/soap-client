<?php

namespace Phpro\SoapClient\CodeGenerator\Config;

use Phpro\SoapClient\CodeGenerator\Assembler;
use Phpro\SoapClient\CodeGenerator\Assembler\ClientMethodAssembler;
use Phpro\SoapClient\CodeGenerator\Model\DuplicateType;
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
    protected $clientName = 'Client';

    /**
     * @var string
     */
    protected $typeNamespace = '';

    /**
     * @var
     */
    protected $clientNamespace = '';

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
    protected $clientDestination = '';

    /**
     * @var string
     */
    protected $typeDestination = '';

    /**
     * @var RuleSetInterface
     */
    protected $ruleSet;

    /**
     * @var WsdlProviderInterface
     */
    protected $wsdlProvider;

    /**
     * @var string
     */
    protected $classMapName;

    /**
     * @var string
     */
    protected $classMapNamespace;

    /**
     * @var string
     */
    protected $classMapDestination;


    /**
     * @var DuplicateType[]
     */
    protected $duplicateTypes;

    /**
     * Config constructor.
     *
     * @param string $wsdl
     * @param string $destination
     */
    public function __construct(string $wsdl = '', string $destination = '')
    {
        $this->setWsdl($wsdl);
        $this->setTypeDestination($destination);
        $this->ruleSet = new RuleSet([
            new Rules\AssembleRule(new Assembler\PropertyAssembler()),
            new Rules\AssembleRule(new Assembler\ClassMapAssembler()),
            new Rules\AssembleRule(new ClientMethodAssembler())
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
     */
    public function getTypeNamespace(): string
    {
        return $this->typeNamespace;
    }

    /**
     * @param string $namespace
     *
     * @return Config
     */
    public function setTypeNamespace($namespace): self
    {
        $this->typeNamespace = Normalizer::normalizeNamespace($namespace);

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
     * @param string $wsdl
     *
     * @return Config
     */
    public function setWsdl($wsdl): self
    {
        $this->wsdl = $wsdl;

        return $this;
    }

    /**
     * @param string $key
     * @param mixed $value
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
     * @param array $soapOptions
     *
     * @return $this
     */
    public function setSoapOptions(array $soapOptions)
    {
        $this->soapOptions = $soapOptions;

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
     * @return string
     */
    public function getClientName(): string
    {
        return $this->clientName;
    }

    /**
     * @param string $clientName
     * @return $this
     */
    public function setClientName($clientName): self
    {
        $this->clientName = $clientName;

        return $this;
    }

    /**
     * @return string
     */
    public function getClientNamespace(): string
    {
        if (!$this->clientNamespace) {
            throw InvalidArgumentException::clientNamespaceIsMissing();
        }

        return $this->clientNamespace;
    }

    /**
     * @param string $clientNamespace
     * @return Config
     */
    public function setClientNamespace($clientNamespace): self
    {
        $this->clientNamespace = $clientNamespace;

        return $this;
    }

    /**
     * @return string
     */
    public function getClientDestination(): string
    {
        if (!$this->clientDestination) {
            throw InvalidArgumentException::clientDestinationIsMissing();
        }

        return $this->clientDestination;
    }

    /**
     * @param string $clientDestination
     * @return Config
     */
    public function setClientDestination($clientDestination): self
    {
        $this->clientDestination = $clientDestination;

        return $this;
    }

    /**
     * @return string
     */
    public function getTypeDestination(): string
    {
        if (!$this->typeDestination) {
            throw InvalidArgumentException::typeDestinationIsMissing();
        }

        return $this->typeDestination;
    }

    /**
     * @param string $typeDestination
     * @return Config
     */
    public function setTypeDestination($typeDestination): self
    {
        $this->typeDestination = $typeDestination;

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

    /**
     * @return string
     */
    public function getClassMapName(): string
    {
        if (!$this->classMapName) {
            throw InvalidArgumentException::classmapNameMissing();
        }

        return $this->classMapName;
    }

    /**
     * @return string
     */
    public function getClassMapNamespace(): string
    {
        if (!$this->classMapNamespace) {
            throw InvalidArgumentException::classmapNamespaceMissing();
        }

        return $this->classMapNamespace;
    }

    /**
     * @return string
     */
    public function getClassMapDestination(): string
    {
        if (!$this->classMapDestination) {
            throw InvalidArgumentException::classmapDestinationMissing();
        }

        return $this->classMapDestination;
    }

    /**
     * @param string $classMapName
     * @return Config
     */
    public function setClassMapName(string $classMapName): self
    {
        $this->classMapName = $classMapName;

        return $this;
    }

    /**
     * @param string $classMapNamespace
     * @return Config
     */
    public function setClassMapNamespace(string $classMapNamespace): self
    {
        $this->classMapNamespace = $classMapNamespace;

        return $this;
    }

    /**
     * @param string $classMapDestination
     * @return Config
     */
    public function setClassMapDestination(string $classMapDestination): self
    {
        $this->classMapDestination = $classMapDestination;

        return $this;
    }

    /**
     * @return DuplicateType[]
     */
    public function getDuplicateTypes(): array
    {
        return $this->duplicateTypes;
    }

    /**
     * @param DuplicateType[] $duplicateTypes
     */
    public function setDuplicateTypes(array $duplicateTypes)
    {
        $this->duplicateTypes = $duplicateTypes;

        return $this;
    }
}
