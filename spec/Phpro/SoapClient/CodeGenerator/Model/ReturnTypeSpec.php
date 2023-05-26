<?php

namespace spec\Phpro\SoapClient\CodeGenerator\Model;

use Phpro\SoapClient\CodeGenerator\Model\ReturnType;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Soap\Engine\Metadata\Model\TypeMeta;

/**
 * Class ReturnTypeSpec
 *
 * @package spec\Phpro\SoapClient\CodeGenerator\Model
 * @mixin ReturnType
 */
class ReturnTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('Type', 'My\Namespace', new TypeMeta());
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ReturnType::class);
    }

    function it_has_a_type()
    {
        $this->getType()->shouldReturn('\\My\\Namespace\\Type');
    }

    public function it_has_type_meta(): void
    {
        $this->getMeta()->shouldBeLike(new TypeMeta());
    }
}
