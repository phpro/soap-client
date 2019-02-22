<?php

namespace spec\Phpro\SoapClient\Soap\Engine\Metadata;

use Phpro\SoapClient\Soap\Engine\Metadata\Collection\MethodCollection;
use Phpro\SoapClient\Soap\Engine\Metadata\Collection\TypeCollection;
use Phpro\SoapClient\Soap\Engine\Metadata\MetadataInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Phpro\SoapClient\Soap\Engine\Metadata\LazyInMemoryMetadata;

/**
 * Class LazyInMemoryMetadataSpec
 */
class LazyInMemoryMetadataSpec extends ObjectBehavior
{
    function let(MetadataInterface $metadata)
    {
        $this->beConstructedWith($metadata);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(LazyInMemoryMetadata::class);
    }

    function it_is_metadata()
    {
        $this->shouldImplement(MetadataInterface::class);
    }

    function it_lazy_loads_types_once(MetadataInterface $metadata)
    {
        $metadata->getTypes()->willReturn($collection = new TypeCollection());
        $metadata->getTypes()->shouldBeCalledOnce();

        $this->getTypes()->shouldBe($collection);
        $this->getTypes()->shouldBe($collection);
    }

    function it_lazy_loads_methods_once(MetadataInterface $metadata)
    {
        $metadata->getMethods()->willReturn($collection = new MethodCollection());
        $metadata->getMethods()->shouldBeCalledOnce();

        $this->getMethods()->shouldBe($collection);
        $this->getMethods()->shouldBe($collection);
    }
}
