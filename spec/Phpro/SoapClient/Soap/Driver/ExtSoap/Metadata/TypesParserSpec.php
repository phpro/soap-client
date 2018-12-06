<?php

namespace spec\Phpro\SoapClient\Soap\Driver\ExtSoap\Metadata;

use Phpro\SoapClient\Soap\Driver\ExtSoap\AbusedClient;
use Phpro\SoapClient\Soap\Engine\Metadata\Collection\TypeCollection;
use Phpro\SoapClient\Soap\Engine\Metadata\Model\Property;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Phpro\SoapClient\Soap\Driver\ExtSoap\Metadata\TypesParser;

/**
 * Class TypesParserSpec
 */
class TypesParserSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(TypesParser::class);
    }

    function it_can_parse_ext_soap_types_strings_with_single_argument()
    {
        $abusedClient = $this->mockAbusedClient([
            <<<EOSTRUCT
struct ProductLine {
 string Mode;
 string RelevanceRank;
 ProductInfo ProductInfo;
}
EOSTRUCT
        ]);

        $types = $this->parse($abusedClient);
        $types->shouldHaveType(TypeCollection::class);
        $types->shouldHaveCount(1);

        $type = $types->fetchByName('ProductLine');
        $type->getName()->shouldBe('ProductLine');

        $properties = $type->getProperties();
        $properties->shouldHaveCount(3);

        $properties[0]->shouldHaveType(Property::class);
        $properties[0]->getName()->shouldBe('Mode');
        $properties[0]->getType()->shouldBe('string');

        $properties[1]->shouldHaveType(Property::class);
        $properties[1]->getName()->shouldBe('RelevanceRank');
        $properties[1]->getType()->shouldBe('string');

        $properties[2]->shouldHaveType(Property::class);
        $properties[2]->getName()->shouldBe('ProductInfo');
        $properties[2]->getType()->shouldBe('ProductInfo');
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
