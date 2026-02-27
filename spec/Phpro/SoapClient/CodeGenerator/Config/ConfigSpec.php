<?php

namespace spec\Phpro\SoapClient\CodeGenerator\Config;

use Phpro\SoapClient\CodeGenerator\Config\ClientConfig;
use Phpro\SoapClient\CodeGenerator\Config\ClassMapConfig;
use Phpro\SoapClient\CodeGenerator\Config\Config;
use Phpro\SoapClient\CodeGenerator\Config\Destination;
use Phpro\SoapClient\CodeGenerator\Config\TypeNamespaceMap;
use Phpro\SoapClient\CodeGenerator\Rules\RuleSet;
use Phpro\SoapClient\Exception\InvalidArgumentException;
use PhpSpec\ObjectBehavior;
use Soap\Engine\Engine;

/**
 * Class ConfigSpec
 *
 * @package spec\Phpro\SoapClient\CodeGenerator\Config
 * @mixin Config
 */
class ConfigSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Config::class);
    }

    function it_has_an_engine(Engine $engine)
    {
        $this->setEngine($engine);
        $this->getEngine()->shouldReturn($engine);
    }

    function it_requires_an_engine()
    {
        $this->shouldThrow(InvalidArgumentException::class)->duringGetEngine();
    }

    function it_requires_a_type_namespace_map()
    {
        $this->shouldThrow(InvalidArgumentException::class)->duringGetTypeNamespaceMap();
    }

    function it_has_a_ruleset()
    {
        $this->setRuleSet($value = new RuleSet([]));
        $this->getRuleSet()->shouldBe($value);
    }

    public function it_has_a_type_namespace_map()
    {
        $destination = new Destination('src/type', 'TypeNamespace');
        $value = TypeNamespaceMap::create($destination);
        $this->setTypeNamespaceMap($value);
        $this->getTypeNamespaceMap()->shouldBe($value);
    }

    public function it_has_a_client_config()
    {
        $destination = new Destination('src/client', 'ClientNamespace');
        $value = new ClientConfig('ClientName', $destination);
        $this->setClient($value);
        $this->getClient()->shouldBe($value);
    }

    public function it_requires_a_client_config()
    {
        $this->shouldThrow(InvalidArgumentException::class)->duringGetClient();
    }

    public function it_has_a_classmap_config()
    {
        $destination = new Destination('src/classmap', 'ClassMapNamespace');
        $value = new ClassMapConfig('ClassMapName', $destination);
        $this->setClassMap($value);
        $this->getClassMap()->shouldBe($value);
    }

    public function it_requires_a_classmap_config()
    {
        $this->shouldThrow(InvalidArgumentException::class)->duringGetClassMap();
    }
}
