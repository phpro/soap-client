<?php

namespace spec\Phpro\SoapClient\CodeGenerator;

use Phpro\SoapClient\CodeGenerator\Context\ClientFactoryContext;
use Phpro\SoapClient\CodeGenerator\GeneratorInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Phpro\SoapClient\CodeGenerator\ClientFactoryGenerator;
use Zend\Code\Generator\FileGenerator;

/**
 * Class ClientFactoryGeneratorSpec
 */
class ClientFactoryGeneratorSpec extends ObjectBehavior
{
    private $body = <<<BODY
<?php

namespace App\Classmap;

use App\Client\Myclient;
use App\Classmap\Myclassmap;
use Phpro\SoapClient\ClientFactory as PhproClientFactory;
use Phpro\SoapClient\ClientBuilder;

class MyclientFactory
{

    public static function factory(string \$wsdl) : \App\Client\Myclient
    {
        \$clientFactory = new PhproClientFactory(Myclient::class);
        \$clientBuilder = new ClientBuilder(\$clientFactory, \$wsdl, []);
        \$clientBuilder->withClassMaps(Myclassmap::getCollection());

        return \$clientBuilder->build();
    }


}


BODY;


    function it_is_initializable()
    {
        $this->shouldHaveType(ClientFactoryGenerator::class);
    }

    function it_is_a_generator()
    {
        $this->shouldImplement(GeneratorInterface::class);
    }

    function it_generates_a_client_factory(ClientFactoryContext $context)
    {
        $context->getClientName()->willReturn('Myclient');
        $context->getClientNamespace()->willReturn('App\\Client');
        $context->getClassmapName()->willReturn('Myclassmap');
        $context->getClientNamespace()->willReturn('App\\Classmap');
        $context->getClientFqcn()->willReturn('App\\Client\\Myclient');
        $context->getClassmapFqcn()->willReturn('App\\Classmap\\Myclassmap');
        $file = new FileGenerator();
        $this->generate($file, $context)->shouldBe($this->body);
    }
}
