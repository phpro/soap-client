<?php

namespace spec\Phpro\SoapClient\CodeGenerator\Config;

use Phpro\SoapClient\CodeGenerator\Config\Config;
use Phpro\SoapClient\CodeGenerator\Config\ConfigInterface;
use Phpro\SoapClient\CodeGenerator\Rules\RuleSet;
use Phpro\SoapClient\Exception\InvalidArgumentException;
use Phpro\SoapClient\Exception\WsdlException;
use Phpro\SoapClient\Util\Filesystem;
use Phpro\SoapClient\Wsdl\Provider\LocalWsdlProvider;
use Phpro\SoapClient\Wsdl\Provider\MixedWsdlProvider;
use Phpro\SoapClient\Wsdl\Provider\WsdlProviderInterface;
use PhpSpec\ObjectBehavior;

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

    function it_is_a_config_class()
    {
        $this->shouldImplement(ConfigInterface::class);
    }

    function it_has_a_wsdl()
    {
        $this->setWsdl($value = 'http://myservice/some.wsdl');
        $this->getWsdl()->shouldReturn($value);
    }

    function it_has_a_wsdl_provider()
    {
        $this->getWsdlProvider()->shouldImplement(MixedWsdlProvider::class);
    }

    function it_can_overwrite_wsdl_prover()
    {
        $this->setWsdlProvider($value = new LocalWsdlProvider(new Filesystem()));
        $this->getWsdlProvider()->shouldReturn($value);
    }

    function it_requires_a_wsdl()
    {
        $this->shouldThrow(InvalidArgumentException::class)->duringGetWsdl();
    }

    function it_requires_a_valid_wsdl_provider(WsdlProviderInterface $wsdlProvider)
    {
        $this->setWsdl($wsdl = 'some.wsdl');
        $wsdlProvider->provide($wsdl)->willThrow(WsdlException::class);
        $this->setWsdlProvider($wsdlProvider);
        $this->shouldThrow(InvalidArgumentException::class)->duringGetWsdl();
    }

    function it_requires_a_typedestination()
    {
        $this->shouldThrow(InvalidArgumentException::class)->duringGetTypeDestination();
    }

    function it_has_a_ruleset()
    {
        $this->setRuleSet($value = new RuleSet([]));
        $this->getRuleSet()->shouldBe($value);
    }

    function it_had_soap_options()
    {
        $this->getSoapOptions()->shouldBe(
            [
                'trace' => false,
                'exceptions' => true,
                'keep_alive' => true,
                'cache_wsdl' => WSDL_CACHE_NONE,
            ]
        );

        $this->setSoapOptions($value = []);
        $this->getSoapOptions()->shouldBe($value);

        $this->addSoapOption('key', 'value');
        $this->getSoapOptions()->shouldBe(['key' => 'value']);
    }

    public function it_has_a_type_destination()
    {
        $this->setTypeDestination($value = 'src/type');
        $this->getTypeDestination()->shouldBe($value);
    }

    public function it_has_a_client_destination()
    {
        $this->setClientDestination($value = 'src/client');
        $this->getClientDestination()->shouldBe($value);
    }

    public function it_has_a_type_namespace()
    {
        $this->setTypeNamespace($value = 'TypeNamespace');
        $this->getTypeNamespace()->shouldBe($value);
    }

    public function it_has_a_client_namespace()
    {
        $this->setClientNamespace($value = 'ClientNamespace');
        $this->getClientNamespace()->shouldBe($value);
    }

    public function it_requires_a_client_namespace()
    {
        $this->shouldThrow(InvalidArgumentException::class)->duringGetClientNamespace();
    }

    public function it_has_a_client_name()
    {
        $this->setClientName($value = 'ClientName');
        $this->getClientName()->shouldBe($value);
    }
}
