<?php

namespace spec\Phpro\SoapClient\CodeGenerator\Model;

use Phpro\SoapClient\CodeGenerator\Config\Destination;
use Phpro\SoapClient\CodeGenerator\Config\TypeNamespaceMap;
use Phpro\SoapClient\CodeGenerator\Model\Parameter;
use PhpSpec\ObjectBehavior;
use Soap\Engine\Metadata\Model\TypeMeta;
use Soap\Engine\Metadata\Model\XsdType;

/**
 * Class ParameterSpec
 *
 * @package spec\Phpro\SoapClient\CodeGenerator\Model
 * @mixin Parameter
 */
class ParameterSpec extends ObjectBehavior
{
    function let()
    {
        $destination = new Destination('src/type', 'MyNamespace');
        $namespaceMap = TypeNamespaceMap::create($destination);
        $this->beConstructedWith('MyParameter', 'MyParameterType', $namespaceMap, XsdType::create('MyParameter'));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Parameter::class);
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn('MyParameter');
    }

    function is_has_a_namespace()
    {
        $this->getNamespace()->shouldBe('MyParameterType');
    }

    public function it_has_type_meta(): void
    {
        $this->getMeta()->shouldBeLike(new TypeMeta());
    }
}
