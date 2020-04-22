<?php

namespace spec\Phpro\SoapClient\CodeGenerator\Util;

use Phpro\SoapClient\CodeGenerator\Util\Validator;
use PhpSpec\ObjectBehavior;

/**
 * Class ValidatorSpec
 *
 * @package spec\Phpro\SoapClient\CodeGenerator\Util
 * @mixin Validator
 */
class ValidatorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Validator::class);
    }

    function it_can_tell_what_commands_need_laminas_code()
    {
        $this->commandRequiresLaminasCode('wizard')->shouldBe(true);
        $this->commandRequiresLaminasCode('generate')->shouldBe(true);
        $this->commandRequiresLaminasCode('generate:something')->shouldBe(true);
        $this->commandRequiresLaminasCode('list')->shouldBe(false);
    }

    function it_can_tell_if_laminas_code_is_installed()
    {
        $this->laminasCodeIsInstalled()->shouldBe(true);
    }
}
