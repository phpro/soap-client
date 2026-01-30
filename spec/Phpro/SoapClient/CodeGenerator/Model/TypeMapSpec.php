<?php

namespace spec\Phpro\SoapClient\CodeGenerator\Model;

use Phpro\SoapClient\CodeGenerator\Config\Destination;
use Phpro\SoapClient\CodeGenerator\Config\TypeNamespaceMap;
use Phpro\SoapClient\CodeGenerator\Model\Property;
use Phpro\SoapClient\CodeGenerator\Model\Type;
use Phpro\SoapClient\CodeGenerator\Model\TypeMap;
use PhpSpec\ObjectBehavior;
use Soap\Engine\Metadata\Model\XsdType;

/**
 * Class TypeMapSpec
 *
 * @package spec\Phpro\SoapClient\CodeGenerator\Model
 * @mixin TypeMap
 */
class TypeMapSpec extends ObjectBehavior
{
    function let()
    {
        $destination = new Destination('src/type', 'MyNamespace');
        $namespaceMap = TypeNamespaceMap::create($destination);
        $this->beConstructedWith($namespaceMap, [
            new Type($namespaceMap, 'type1', 'type1', [
                new Property('prop1', 'string', $namespaceMap, 'MyNamespace', XsdType::create('string'))
            ], XsdType::create('MyType'))
        ]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(TypeMap::class);
    }

    function it_has_types()
    {
        $types = $this->getTypes();
        $types[0]->shouldReturnAnInstanceOf(Type::class);
        $types[0]->getXsdName()->shouldBe('type1');
    }
}
