<?php


use Phpro\SoapClient\CodeGenerator\ClientFactoryGenerator;
use Phpro\SoapClient\CodeGenerator\Context\ClassMapContext;
use Phpro\SoapClient\CodeGenerator\Context\ClientContext;
use Phpro\SoapClient\CodeGenerator\Context\ClientFactoryContext;
use PHPUnit\Framework\TestCase;
use Zend\Code\Generator\FileGenerator;

class ClientFactoryGeneratorTest extends TestCase
{
    public function testGenerate()
    {
        $expected = <<<BODY
<?php

namespace App\Client;

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
        $clientContext = new ClientContext('Myclient', 'App\\Client');
        $classMapContext = new ClassMapContext(
            new FileGenerator(),
            new \Phpro\SoapClient\CodeGenerator\Model\TypeMap('App\\Types', []),
            'Myclassmap',
            'App\\Classmap'
        );
        $context = new ClientFactoryContext($clientContext, $classMapContext);
        $generator = new ClientFactoryGenerator();
        self::assertEquals($expected, $generator->generate(new FileGenerator(), $context));
    }
}
