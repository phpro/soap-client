<?php

namespace spec\Phpro\SoapClient\Soap\Engine\Metadata\Collection;

use Phpro\SoapClient\Exception\MetadataException;
use Phpro\SoapClient\Soap\Engine\Metadata\Model\Method;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Phpro\SoapClient\Soap\Engine\Metadata\Collection\MethodCollection;

/**
 * Class MethodCollectionSpec
 */
class MethodCollectionSpec extends ObjectBehavior
{
    function let(Method $method)
    {
        $this->beConstructedWith($method);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(MethodCollection::class);
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

    function it_can_return_iterator(Method $method)
    {
        $iterator = $this->getIterator();
        $iterator->shouldHaveType(\ArrayIterator::class);
        $iterator->shouldIterateAs([$method]);
    }

    function it_can_add_method(Method $method2)
    {
        $this->add($method2);
        $this->getIterator()[1]->shouldBe($method2);
    }

    function it_can_map_over_methods()
    {
        $this->map(function() {
            return 'hello';
        })->shouldBe(['hello']);
    }

    function it_can_find_method_by_name(Method $method)
    {
        $method->getName()->willReturn($name = 'name');
        $this->fetchOneByName($name)->shouldBe($method);
    }

    function it_throws_exception_when_a_method_could_not_be_found_by_name(Method $method)
    {
        $method->getName()->willReturn($name = 'name');
        $this->shouldthrow(MetadataException::class)->duringFetchOneByName('invalid');
    }
}
