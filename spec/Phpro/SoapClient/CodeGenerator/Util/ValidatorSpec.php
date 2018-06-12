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

    function it_can_tell_what_commands_need_zend_code()
    {
        $this->commandRequiresZendCode('wizard')->shouldBe(true);
        $this->commandRequiresZendCode('generate')->shouldBe(true);
        $this->commandRequiresZendCode('generate:something')->shouldBe(true);
        $this->commandRequiresZendCode('list')->shouldBe(false);
    }

    function it_can_tell_if_zend_code_is_installed()
    {
        $this->zendCodeIsInstalled()->shouldBe(true);
    }
}
