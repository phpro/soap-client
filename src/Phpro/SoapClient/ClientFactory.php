<?php

namespace Phpro\SoapClient;

use Phpro\SoapClient\Soap\Engine\EngineInterface;
use ReflectionClass;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ClientFactory implements ClientFactoryInterface
{

    /**
     * @var string
     */
    private $className;

    public function __construct(string $className)
    {
        $this->className = $className;
    }

    public function factory(EngineInterface $engine, EventDispatcherInterface $dispatcher): ClientInterface
    {
        $rc = new ReflectionClass($this->className);

        return $rc->newInstance($engine, $dispatcher);
    }
}
