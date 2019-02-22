<?php

namespace spec\Phpro\SoapClient\Soap\Engine\Metadata\Collection;

use Phpro\SoapClient\Exception\MetadataException;
use Phpro\SoapClient\Soap\Engine\Metadata\Model\Type;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Phpro\SoapClient\Soap\Engine\Metadata\Collection\TypeCollection;

/**
 * Class TypeCollectionSpec
 */
class TypeCollectionSpec extends ObjectBehavior
{
    function let(Type $type)
    {
        $this->beConstructedWith($type);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(TypeCollection::class);
    }

    function it_is_an_iterator_aggregate()
    {
        $this->shouldHaveType(\IteratorAggregate::class);
    }

    function it_is_countable()
    {
        $this->shouldHaveType(\Countable::class);
    }

    function it_has_a_count()
    {
        $this->count()->shouldBe(1);
    }

    function it_can_return_iterator(Type $type)
    {
        $iterator = $this->getIterator();
        $iterator->shouldHaveType(\ArrayIterator::class);
        $iterator->shouldIterateAs([$type]);
    }

    function it_can_add_type(Type $type2)
    {
        $this->add($type2);
        $this->getIterator()[1]->shouldBe($type2);
    }

    function it_can_map_over_types()
    {
        $this->map(function() {
            return 'hello';
        })->shouldBe(['hello']);
    }

    function it_can_find_type_by_name(Type $type)
    {
        $type->getName()->willReturn($name = 'name');
        $this->fetchOneByName($name)->shouldBe($type);
    }

    function it_throws_exception_when_a_type_could_not_be_found_by_name(Type $type)
    {
        $type->getName()->willReturn($name = 'name');
        $this->shouldthrow(MetadataException::class)->duringFetchOneByName('invalid');
    }
}
