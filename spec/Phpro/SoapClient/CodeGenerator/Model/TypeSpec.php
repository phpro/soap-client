<?php

namespace spec\Phpro\SoapClient\CodeGenerator\Model;

use Phpro\SoapClient\CodeGenerator\Config\Destination;
use Phpro\SoapClient\CodeGenerator\Config\TypeNamespaceMap;
use Phpro\SoapClient\CodeGenerator\Model\Property;
use Phpro\SoapClient\CodeGenerator\Model\Type;
use Phpro\SoapClient\CodeGenerator\Util\Normalizer;
use PhpSpec\ObjectBehavior;
use Soap\Engine\Metadata\Model\TypeMeta;
use Soap\Engine\Metadata\Model\XsdType;

/**
 * Class TypeSpec
 *
 * @package spec\Phpro\SoapClient\CodeGenerator\Model
 * @mixin Type
 */
class TypeSpec extends ObjectBehavior
{
    function let()
    {
        $destination = new Destination('src/type', 'MyNamespace');
        $namespaceMap = TypeNamespaceMap::create($destination);
        $this->beConstructedWith(
            $namespaceMap,
            'myType',
            'MyType',
            [new Property('prop1', 'string', $namespaceMap, 'MyNamespace', XsdType::create('string'))],
            XsdType::create('MyType')
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Type::class);
    }

    function it_has_a_namespace()
    {
        $this->getNamespace()->shouldReturn('MyNamespace');
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn('MyType');
    }

    function it_has_a_xsd_type()
    {
        $this->getXsdName()->shouldReturn('myType');
    }

    function it_has_a_full_name()
    {
        $this->getFullName()->shouldReturn('MyNamespace\\MyType');
    }
    function it_has_meta()
    {
        $this->getMeta()->shouldBeLike(new TypeMeta());
    }

    function it_should_not_replace_underscores_in_paths()
    {
        $destination = new Destination('my/some_dir', 'MyNamespace');
        $namespaceMap = TypeNamespaceMap::create($destination);
        $this->beConstructedWith($namespaceMap, 'my_type_3_2', Normalizer::normalizeClassname('my_type_3_2'), ['prop1' => 'string'], XsdType::create('MyType'));
        $this->getFileInfo()->getPathname()->shouldReturn('my/some_dir/MyType32.php');
    }

    function it_should_prefix_reserved_keywords()
    {
        $destination = new Destination('my/some_dir', 'MyNamespace');
        $namespaceMap = TypeNamespaceMap::create($destination);
        $this->beConstructedWith(
            $namespaceMap,
            'Final',
            Normalizer::normalizeClassname('Final'),
            [new Property('xor', 'string', $namespaceMap, 'MyNamespace', XsdType::create('string'))],
            XsdType::create('MyType')
        );

        $this->getFileInfo()->getPathname()->shouldReturn('my/some_dir/FinalType.php');
        $this->getName()->shouldReturn('FinalType');
        $this->getProperties()[0]->getName()->shouldReturn('xor');
    }

    function it_has_properties()
    {
        $props = $this->getProperties();
        $props[0]->shouldReturnAnInstanceOf(Property::class);
        $props[0]->getName()->shouldReturn('prop1');
        $props[0]->getType()->shouldReturn('string');
    }
}
