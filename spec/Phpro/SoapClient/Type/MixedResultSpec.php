<?php

namespace spec\Phpro\SoapClient\Type;

use Phpro\SoapClient\Type\MixedResult;
use Phpro\SoapClient\Type\ResultInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class MixedResultSpec
 *
 * @package spec\Phpro\SoapClient\Type
 * @mixin MixedResult
 */
class MixedResultSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('actualResult');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(MixedResult::class);
    }

    function it_is_a_result()
    {
        $this->shouldImplement(ResultInterface::class);
    }

    function it_contains_the_mixed_result()
    {
        $this->getResult()->shouldBe('actualResult');
    }
}
