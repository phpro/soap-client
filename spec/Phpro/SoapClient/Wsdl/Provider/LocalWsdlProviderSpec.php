<?php

namespace spec\Phpro\SoapClient\Wsdl\Provider;

use Phpro\SoapClient\Exception\WsdlException;
use Phpro\SoapClient\Util\Filesystem;
use Phpro\SoapClient\Wsdl\Provider\LocalWsdlProvider;
use Phpro\SoapClient\Wsdl\Provider\WsdlProviderInterface;
use PhpSpec\ObjectBehavior;

class LocalWsdlProviderSpec extends ObjectBehavior
{
    function let(Filesystem $filesystem)
    {
        $this->beConstructedWith($filesystem);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(LocalWsdlProvider::class);
    }

    function it_is_a_wsdl_provider()
    {
        $this->shouldImplement(WsdlProviderInterface::class);
    }

    function it_provides_an_existing_file(Filesystem $filesystem)
    {
        $filesystem->fileExists($file = 'some.wsdl')->willReturn(true);
        $this->provide($file)->shouldBe($file);
    }

    function it_throws_an_exception_if_a_file_does_not_exist(Filesystem $filesystem)
    {
        $filesystem->fileExists($file = 'some.wsdl')->willReturn(false);
        $this->shouldThrow(WsdlException::class)->duringProvide($file);
    }
}
