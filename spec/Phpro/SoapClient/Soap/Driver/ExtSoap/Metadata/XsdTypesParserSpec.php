<?php

namespace spec\Phpro\SoapClient\Soap\Driver\ExtSoap\Metadata;

use Phpro\SoapClient\Soap\Driver\ExtSoap\AbusedClient;
use Phpro\SoapClient\Soap\Driver\ExtSoap\Metadata\Visitor\XsdTypeVisitorInterface;
use Phpro\SoapClient\Soap\Engine\Metadata\Collection\XsdTypeCollection;
use Phpro\SoapClient\Soap\Engine\Metadata\Model\XsdType;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Phpro\SoapClient\Soap\Driver\ExtSoap\Metadata\XsdTypesParser;

/**
 * Class XsdTypesParserSpec
 */
class XsdTypesParserSpec extends ObjectBehavior
{
    function let(XsdTypeVisitorInterface $visitor1, XsdTypeVisitorInterface $visitor2)
    {
        $this->beConstructedWith($visitor1, $visitor2);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(XsdTypesParser::class);
    }

    function it_can_parse_ext_soap_types_strings_with_single_argument(
        XsdTypeVisitorInterface $visitor1,
        XsdTypeVisitorInterface $visitor2
    ) {
        $abusedClient = $this->mockAbusedClient([
            $typeString1 = 'string simpleType1',
            $typeString2 = 'string simpleType2',
            $typeString3 = 'struct invalid xsdtype {}'
        ]);

        $visitor1->__invoke($typeString1)->willReturn($type1 = XsdType::create('simpleType1'));
        $visitor1->__invoke($typeString2)->willReturn(null);
        $visitor1->__invoke($typeString3)->willReturn(null);

        $visitor2->__invoke($typeString1)->shouldNotBeCalled();
        $visitor2->__invoke($typeString2)->willReturn($type2 = XsdType::create('simpleType2'));
        $visitor2->__invoke($typeString3)->willReturn(null);

        $result =$this->parse($abusedClient);
        $result->shouldHaveType(XsdTypeCollection::class);
        $result->shouldHaveCount(2);
        $result->shouldIterateAs([$type1, $type2]);
    }

    function it_contains_a_default_set_of_visitors()
    {
        $this->beConstructedThrough('default', []);
        $abusedClient = $this->mockAbusedClient([
            $unionString1 = 'union unionType {member1,member2}',
            $unionString2 = 'union unionType',
            $listString1 = 'list listType {member1,member2}',
            $listString2 = 'list listType',
            $simpleTypeString = 'string simpleType',
            $structString = 'struct invalid xsdtype {}'
        ]);

        $result = $this->parse($abusedClient);
        $result->shouldHaveCount(5);
        $iterator = $result->getIterator();
        $iterator[0]->getName()->shouldBe('unionType');
        $iterator[1]->getName()->shouldBe('unionType');
        $iterator[2]->getName()->shouldBe('listType');
        $iterator[3]->getName()->shouldBe('listType');
        $iterator[4]->getName()->shouldBe('simpleType');
    }

    function it_can_handle_double_typenames_in_separate_namespaces()
    {
        $this->beConstructedThrough('default', []);
        $abusedClient = $this->mockAbusedClient([
            $typeString1 = 'string simpleType',
            $typeString2 = 'integer simpleType',
        ]);

        $result = $this->parse($abusedClient);
        $result->shouldHaveCount(2);
        $iterator = $result->getIterator();
        $iterator[0]->getName()->shouldBe('simpleType');
        $iterator[0]->getBaseType()->shouldBe('string');
        $iterator[1]->getName()->shouldBe('simpleType');
        $iterator[1]->getBaseType()->shouldBe('integer');
        $result->fetchByNameWithFallback('simpleType')->getBaseType()->shouldBe('string');
    }

    /**
     * Phpspec cant mock the __getTypes()
     */
    private function mockAbusedClient(array $types): AbusedClient
    {
        return new class($types) extends AbusedClient {
            /** @var array */
            private $types;

            public function __construct(array $types)
            {
                $this->types = $types;
            }

            public function __getTypes()
            {
                return $this->types;
            }
        };
    }
}
