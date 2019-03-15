<?php

namespace spec\Phpro\SoapClient\Soap\Engine\Metadata\Model;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Phpro\SoapClient\Soap\Engine\Metadata\Model\XsdType;

/**
 * Class XsdTypeSpec
 */
class XsdTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedThrough('create', ['myType']);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(XsdType::class);
    }

    function it_contains_a_name()
    {
        $this->getName()->shouldBe('myType');
    }

    function it_doesnt_contain_other_metadata_than_name_on_initialisation()
    {
        $this->getBaseTypeOrFallbackToName()->shouldBe('myType');
        $this->getName()->shouldBe('myType');
        $this->getXmlNamespace()->shouldBe('');
        $this->getXmlNamespaceName()->shouldBe('');
        $this->getBaseType()->shouldBe('');
        $this->getMemberTypes()->shouldBe([]);
    }

    function it_cannot_guess_unknown_types()
    {
        $type = $this->guess('myType');
        $type->getName()->shouldBe('myType');
        $type->getBaseType()->shouldBe('');
    }

    function it_can_guess_known_types()
    {
        foreach (XsdType::fetchAllKnownBaseTypeMappings() as $typeName => $baseType) {
            $type = $this->guess($typeName);
            $type->getName()->shouldBe($typeName);
            $type->getBaseType()->shouldBe($baseType);
        }
    }

    function it_can_add_base_type()
    {
        $new = $this->withBaseType('baseType');
        $new->shouldNotBe($this);
        $new->getName()->shouldBe('myType');
        $new->getBaseType()->shouldBe('baseType');
        $new->getBaseTypeOrFallbackToName()->shouldBe('baseType');
    }

    function it_can_add_known_base_type_and_move_actual_type_to_member_types()
    {
        foreach (XsdType::fetchAllKnownBaseTypeMappings() as $typeName => $baseType) {
            $new = $this->withBaseType($typeName);
            $new->shouldNotBe($this);
            $new->getName()->shouldBe('myType');
            $new->getBaseType()->shouldBe($baseType);
            $new->getBaseTypeOrFallbackToName()->shouldBe($baseType);
            $new->getMemberTypes()->shouldBe([$typeName]);
        }
    }

    function it_can_add_member_types()
    {
        $new = $this->withMemberTypes($types = ['type1', 'type2']);
        $new->shouldNotBe($this);
        $new->getName()->shouldBe('myType');
        $new->getMemberTypes()->shouldBe($types);
    }

    function it_can_add_xml_namespace()
    {
        $new = $this->withXmlNamespace($namespace = 'http://www.w3.org/2001/XMLSchema');
        $new->shouldNotBe($this);
        $new->getName()->shouldBe('myType');
        $new->getXmlNamespace()->shouldBe($namespace);
    }

    function it_can_add_xml_namespace_name()
    {
        $new = $this->withXmlNamespaceName($namespaceName = 'xsd');
        $new->shouldNotBe($this);
        $new->getName()->shouldBe('myType');
        $new->getXmlNamespaceName()->shouldBe($namespaceName);
    }

    function it_can_return_name_as_string()
    {
        $this->__toString()->shouldBe('myType');
    }
}
