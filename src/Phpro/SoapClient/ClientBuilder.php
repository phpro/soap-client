<?php

declare(strict_types=1);

namespace Phpro\SoapClient;

use Phpro\SoapClient\Plugin\LogPlugin;
use Phpro\SoapClient\Plugin\ValidatorPlugin;
use Phpro\SoapClient\Soap\Driver\ExtSoap\ExtSoapDriver;
use Phpro\SoapClient\Soap\Driver\ExtSoap\ExtSoapOptions;
use Phpro\SoapClient\Soap\Driver\ExtSoap\Handler\ExtSoapClientHandle;
use Phpro\SoapClient\Soap\Engine\DriverInterface;
use Phpro\SoapClient\Soap\Engine\Engine;
use Phpro\SoapClient\Soap\Engine\EngineInterface;
use Phpro\SoapClient\Soap\Handler\HandlerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ClientBuilder
{
    /**
     * @var ClientFactoryInterface
     */
    private $clientFactory;

    /**
     * @var EngineInterface
     */
    private $engine;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var LoggerInterface|null
     */
    private $logger;

    /**
     * @var ValidatorInterface|null
     */
    private $validator;

    public function __construct(ClientFactoryInterface $clientFactory, EngineInterface $engine)
    {
        $this->clientFactory = $clientFactory;
        $this->engine = $engine;
        $this->dispatcher = new EventDispatcher();
    }

    public static function fromExtSoap(ClientFactoryInterface $clientFactory, ExtSoapOptions $options): ClientBuilder
    {
        $driver = ExtSoapDriver::createFromOptions($options);
        $handler = new ExtSoapClientHandle($driver->getClient());

        return self::fromDriverAndHandler($clientFactory, $driver, $handler);
    }

    public static function fromDriverAndHandler(
        ClientFactoryInterface $clientFactory,
        DriverInterface $driver,
        HandlerInterface $handler
    ): ClientBuilder {
        return new self($clientFactory, new Engine($driver, $handler));
    }

    public function withLogger(LoggerInterface $logger): self
    {
        $this->logger = $logger;

        return $this;
    }

    public function withValidator(ValidatorInterface $validator)
    {
        $this->validator = $validator;

        return $this;
    }

    public function withEventDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;

        return $this;
    }

    public function build(): ClientInterface
    {
        if ($this->logger) {
            $this->dispatcher->addSubscriber(new LogPlugin($this->logger));
        }

        if ($this->validator) {
            $this->dispatcher->addSubscriber(new ValidatorPlugin($this->validator));
        }

        return $this->clientFactory->factory($this->engine, $this->dispatcher);
    }
}
