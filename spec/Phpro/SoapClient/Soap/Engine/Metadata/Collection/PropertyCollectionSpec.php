<?php

namespace spec\Phpro\SoapClient\Soap\Engine\Metadata\Collection;

use Phpro\SoapClient\Exception\MetadataException;
use Phpro\SoapClient\Soap\Engine\Metadata\Model\Property;
use PhpSpec\ObjectBehavior;
use Phpro\SoapClient\Soap\Engine\Metadata\Collection\PropertyCollection;

/**
 * Class PropertyCollectionSpec
 */
class PropertyCollectionSpec extends ObjectBehavior
{
    function let(Property $property)
    {
        $this->beConstructedWith($property);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PropertyCollection::class);
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

    function it_can_return_iterator(Property $property)
    {
        $iterator = $this->getIterator();
        $iterator->shouldHaveType(\ArrayIterator::class);
        $iterator->shouldIterateAs([$property]);
    }

    function it_can_map_over_properties()
    {
        $this->map(function() {
            return 'hello';
        })->shouldBe(['hello']);
    }

    function it_can_map_names(Property $property)
    {
        $property->getName()->willReturn('name');
        $this->mapNames()->shouldBe(['name']);
    }

    function it_can_filter_uniques(Property $property1, Property $property2, Property $property3)
    {
        $property1->getName()->willReturn('prop');
        $property2->getName()->willReturn('prop');
        $property3->getName()->willReturn('prop2');

        $this->beConstructedWith($property1, $property2, $property3);
        $new = $this->unique();
        $this->shouldNotBe($new);
        $new->shouldIterateAs([$property2, $property3]);
    }
}
