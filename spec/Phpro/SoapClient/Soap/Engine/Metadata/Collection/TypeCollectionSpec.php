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

    public function it_can_filter_types(Type $type1, Type $type2): void
    {
        $this->beConstructedWith($type1, $type2);
        $new = $this->filter(function (Type $type) use ($type1) {
            return $type1->getWrappedObject() === $type;
        });

        $this->shouldNotBe($new);
        $new->shouldBeAnInstanceOf(TypeCollection::class);
        $new->shouldIterateAs([$type1]);
    }

    public function it_can_reduce(Type $type1, Type $type2): void
    {
        $this->beConstructedWith($type1, $type2);
        $result = $this->reduce(
            function (int $carry, Type $type) {
                return $carry + 1;
            },
            0
        );

        $result->shouldBe(2);
    }

    public function it_can_fetch_multiple_by_normalized_name(Type $type1, Type $type2, Type $type3, Type $type4): void
    {
        $this->beConstructedWith($type1, $type2, $type3, $type4);

        $type1->getName()->willReturn('file');
        $type2->getName()->willReturn('File');
        $type3->getName()->willReturn('-File');
        $type4->getName()->willReturn('SomethingElse');

        $result = $this->fetchAllByNormalizedName('File');

        $this->shouldNotBe($result);
        $result->shouldBeAnInstanceOf(TypeCollection::class);
        $result->shouldIterateAs([$type1, $type2, $type3]);
    }
}
