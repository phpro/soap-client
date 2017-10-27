<?php

/*
 * This file is part of the Phpro application.
 *
 * Copyright (c) 2015-2017 Phpro
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
