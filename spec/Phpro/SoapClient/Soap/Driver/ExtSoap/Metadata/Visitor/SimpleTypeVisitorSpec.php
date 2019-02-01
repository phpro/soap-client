<?php

namespace spec\Phpro\SoapClient\Soap\Driver\ExtSoap\Metadata\Visitor;

use Phpro\SoapClient\Soap\Driver\ExtSoap\Metadata\Visitor\XsdTypeVisitorInterface;
use Phpro\SoapClient\Soap\Engine\Metadata\Model\XsdType;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Phpro\SoapClient\Soap\Driver\ExtSoap\Metadata\Visitor\SimpleTypeVisitor;

/**
 * Class SimpleTypeVisitorSpec
 */
class SimpleTypeVisitorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(SimpleTypeVisitor::class);
    }

    function it_is_an_xsd_type_visitor()
    {
        $this->shouldHaveType(XsdTypeVisitorInterface::class);
    }

    function it_returns_null_on_invalid_entry()
    {
        $this('list listType {,member1,member2}')->shouldBe(null);
        $this('list listType')->shouldBe(null);
        $this('union unionType {,member1,member2}')->shouldBe(null);
        $this('union unionType')->shouldBe(null);
        $this('struct x {}')->shouldBe(null);
    }

    function it_returns_type_on_valid_entry()
    {
        $this('string simpleType')->shouldBeLike(
            XsdType::create('simpleType')
               ->withBaseType('string')
        );
    }
}
