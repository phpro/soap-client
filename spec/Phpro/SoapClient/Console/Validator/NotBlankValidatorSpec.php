<?php

namespace spec\Phpro\SoapClient\Console\Validator;

use Phpro\SoapClient\Console\Validator\NotBlankValidator;
use PhpSpec\ObjectBehavior;
use Symfony\Component\Console\Exception\LogicException;

/**
 * Class RequiredQuestionValidatorSpec
 * @package spec\Phpro\SoapClient\Event
 */
class NotBlankValidatorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(NotBlankValidator::class);
    }

    function it_should_validate_not_blank()
    {
        $this->__invoke('test')->shouldBe('test');
        $this->shouldThrow(LogicException::class)->during('__invoke', [null]);
        $this->shouldThrow(LogicException::class)->during('__invoke', ['']);
    }
}
