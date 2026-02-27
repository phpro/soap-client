<?php

namespace spec\Phpro\SoapClient\CodeGenerator\Model;

use Phpro\SoapClient\CodeGenerator\CodingStandards\DefaultCodingStandardsStrategy;
use Phpro\SoapClient\CodeGenerator\Config\Destination;
use Phpro\SoapClient\CodeGenerator\Config\TypeNamespaceMap;
use Phpro\SoapClient\CodeGenerator\Context\CodeGeneratorContext;
use Phpro\SoapClient\CodeGenerator\Model\ReturnType;
use PhpSpec\ObjectBehavior;
use Soap\Engine\Metadata\Model\TypeMeta;
use Soap\Engine\Metadata\Model\XsdType;

/**
 * Class ReturnTypeSpec
 *
 * @package spec\Phpro\SoapClient\CodeGenerator\Model
 * @mixin ReturnType
 */
class ReturnTypeSpec extends ObjectBehavior
{
    function let()
    {
        $destination = new Destination('src/type', 'My\Namespace');
        $namespaceMap = TypeNamespaceMap::create($destination);
        $context = new CodeGeneratorContext($namespaceMap, new DefaultCodingStandardsStrategy());
        $this->beConstructedWith('Type', $context, XsdType::create('Type'));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ReturnType::class);
    }

    function it_has_a_type()
    {
        $this->getType()->shouldReturn('\\My\\Namespace\\Type');
    }

    function it_can_fall_back_to_mixed_on_unkown_extension_of_simple_type()
    {
        $destination = new Destination('src/type', 'My\Namespace');
        $namespaceMap = TypeNamespaceMap::create($destination);
        $context = new CodeGeneratorContext($namespaceMap, new DefaultCodingStandardsStrategy());
        $this->beConstructedWith(
            'Type',
            $context,
            XsdType::create('Type')
                ->withMeta(static fn (TypeMeta $meta) => $meta->withIsSimple(true))
        );

        $this->getType()->shouldReturn('mixed');
    }

    function it_can_fall_back_to_simple_type()
    {
        $destination = new Destination('src/type', 'My\Namespace');
        $namespaceMap = TypeNamespaceMap::create($destination);
        $context = new CodeGeneratorContext($namespaceMap, new DefaultCodingStandardsStrategy());
        $this->beConstructedWith(
            'Type',
            $context,
            XsdType::create('Type')
                ->withMeta(static fn (TypeMeta $meta) => $meta->withIsSimple(true)->withExtends([
                    'isSimple' => true,
                    'type' => 'string',
                    'namespace' => 'http://www.w3.org/2001/XMLSchema',
                ]))
        );

        $this->getType()->shouldReturn('string');
    }

    public function it_has_type_meta(): void
    {
        $this->getMeta()->shouldBeLike(new TypeMeta());
    }

    public function it_falls_back_to_complex_type_if_its_an_element_referencing_this_type(): void
    {
        $destination = new Destination('src/type', 'My\Namespace');
        $namespaceMap = TypeNamespaceMap::create($destination);
        $context = new CodeGeneratorContext($namespaceMap, new DefaultCodingStandardsStrategy());
        $this->beConstructedThrough(
            'fromMetaData',
            [
                $context,
                XsdType::create('ElementType')
                    ->withXmlTypeName('ComplexType')
            ]
        );

        $this->getType()->shouldReturn('\\My\\Namespace\\ComplexType');
    }
}
