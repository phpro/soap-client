<?php

namespace spec\Phpro\SoapClient\Soap\Engine\Metadata\Collection;

use Phpro\SoapClient\Exception\MetadataException;
use Phpro\SoapClient\Soap\Engine\Metadata\Model\XsdType;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Phpro\SoapClient\Soap\Engine\Metadata\Collection\XsdTypeCollection;

/**
 * Class XsdTypeCollectionSpec
 */
class XsdTypeCollectionSpec extends ObjectBehavior
{
    function let(XsdType $XsdType)
    {
        $this->beConstructedWith($XsdType);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(XsdTypeCollection::class);
    }

    function it_is_an_iterator_aggregate()
    {
        $this->shouldImplement(\IteratorAggregate::class);
    }

    function it_is_countable()
    {
        $this->shouldImplement(\Countable::class);
    }

    function it_has_a_count()
    {
        $this->count()->shouldBe(1);
    }

    function it_can_return_iterator(XsdType $XsdType)
    {
        $iterator = $this->getIterator();
        $iterator->shouldHaveType(\ArrayIterator::class);
        $iterator->shouldIterateAs([$XsdType]);
    }

    function it_can_add_XsdType(XsdType $XsdType2)
    {
        $this->add($XsdType2);
        $this->getIterator()[1]->shouldBe($XsdType2);
    }

    function it_can_map_over_XsdTypes()
    {
        $this->map(function() {
            return 'hello';
        })->shouldBe(['hello']);
    }

    function it_can_find_XsdType_by_name(XsdType $XsdType)
    {
        $XsdType->getName()->willReturn($name = 'name');
        $this->fetchByNameWithFallback($name)->shouldBe($XsdType);
        $this->fetchByNameWithFallback('unknown')->shouldBeLike(XsdType::guess('unknown'));
    }
}
