<?php

/*
 * This file is part of the Phpro application.
 *
 * Copyright (c) 2015-2017 Phpro
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Phpro\SoapClient\Wsdl\Provider;

use Phpro\SoapClient\Wsdl\Provider\MixedWsdlProvider;
use Phpro\SoapClient\Wsdl\Provider\WsdlProviderInterface;
use PhpSpec\ObjectBehavior;

class MixedWsdlProviderSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(MixedWsdlProvider::class);
    }

    function it_is_a_wsdl_provider()
    {
        $this->shouldImplement(WsdlProviderInterface::class);
    }

    function it_provides_the_source_as_destination()
    {
        $this->provide('source')->shouldBe('source');
    }
}
