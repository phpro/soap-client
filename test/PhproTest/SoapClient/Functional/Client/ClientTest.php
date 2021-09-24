<?php
declare(strict_types=1);

namespace PhproTest\SoapClient\Functional;

use Http\Client\Common\PluginClient;
use Http\Client\Plugin\Vcr\NamingStrategy\PathNamingStrategy;
use Http\Client\Plugin\Vcr\Recorder\FilesystemRecorder;
use Http\Client\Plugin\Vcr\RecordPlugin;
use Http\Client\Plugin\Vcr\ReplayPlugin;
use Http\Discovery\Psr18ClientDiscovery;
use Phpro\SoapClient\Caller\Caller;
use Phpro\SoapClient\Caller\EngineCaller;
use Phpro\SoapClient\Soap\DefaultEngineFactory;
use Phpro\SoapClient\Type\MixedResult;
use Phpro\SoapClient\Type\MultiArgumentRequest;
use PHPUnit\Framework\TestCase;
use Soap\ExtSoapEngine\ExtSoapOptions;
use Soap\Psr18Transport\Psr18Transport;

class ClientTest extends TestCase
{
    private $client;

    protected function setUp(): void
    {
        $recorder = new FilesystemRecorder(VCR_CASSETTE_DIR.'/client');
        $namingStrategy = new PathNamingStrategy();
        $caller = new EngineCaller(
            DefaultEngineFactory::create(
                ExtSoapOptions::defaults(FIXTURE_DIR.'/wsdl/functional/calculator.wsdl'),
                Psr18Transport::createForClient(
                    new PluginClient(
                        Psr18ClientDiscovery::find(),
                        [
                            new RecordPlugin($namingStrategy, $recorder),
                            new ReplayPlugin($namingStrategy, $recorder, false),
                        ]
                    )
                )
            )
        );

        $this->client = new class ($caller) {
            public function __construct(
                private Caller $caller
            ){
            }

            public function add(MultiArgumentRequest $request): MixedResult
            {
                return ($this->caller)('Add', $request);
            }
        };
    }

    /** @test */
    public function it_can_request_soap_endpoint(): void
    {
        $response = $this->client->add(new MultiArgumentRequest([
            ['intA' => 1, 'intB' => 2]
        ]));

        self::assertSame(3, $response->getResult()->AddResult);
    }
}
