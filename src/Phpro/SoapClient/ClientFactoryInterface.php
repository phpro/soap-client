<?php

namespace Phpro\SoapClient;

use Phpro\SoapClient\Soap\Engine\EngineInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

interface ClientFactoryInterface
{
    public function factory(EngineInterface $engine, EventDispatcherInterface $dispatcher): ClientInterface;
}
