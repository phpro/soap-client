<?php

namespace Phpro\SoapClient;

use Phpro\SoapClient\Exception\InvalidArgumentException;
use Phpro\SoapClient\Middleware\MiddlewareInterface;
use Phpro\SoapClient\Middleware\MiddlewareSupportingInterface;
use Phpro\SoapClient\Plugin\LogPlugin;
use Phpro\SoapClient\Soap\ClassMap\ClassMapCollection;
use Phpro\SoapClient\Soap\ClassMap\ClassMapInterface;
use Phpro\SoapClient\Soap\Handler\HandlerInterface;
use Phpro\SoapClient\Soap\SoapClientFactory;
use Phpro\SoapClient\Soap\TypeConverter;
use Phpro\SoapClient\Soap\TypeConverter\TypeConverterCollection;
use Phpro\SoapClient\Soap\TypeConverter\TypeConverterInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class ClientBuilder
 *
 * @package Phpro\SoapClient
 */
class ClientBuilder
{

    /**
     * @var ClientFactoryInterface
     */
    private $clientFactory;

    /**
     * @var ClassMapCollection
     */
    private $classMaps;

    /**
     * @var TypeConverterCollection
     */
    private $converters;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var LoggerInterface|null
     */
    private $logger;

    /**
     * @var HandlerInterface|null
     */
    private $handler;

    /**
     * @var string
     */
    private $wsdl;

    /**
     * @var array
     */
    private $soapOptions;

    /**
     * @var MiddlewareInterface[]
     */
    private $middlewares = [];

    /**
     * @param ClientFactoryInterface $clientFactory
     * @param string                 $wsdl
     * @param array                  $soapOptions
     */
    public function __construct(ClientFactoryInterface $clientFactory, $wsdl, array $soapOptions = [])
    {
        $this->classMaps = new ClassMapCollection();
        $this->converters = new TypeConverterCollection();
        $this->dispatcher = new EventDispatcher();
        $this->clientFactory = $clientFactory;
        $this->wsdl = $wsdl;
        $this->soapOptions = $soapOptions;

        // Add default converters:
        $this->addTypeConverter(new TypeConverter\DateTimeTypeConverter());
        $this->addTypeConverter(new TypeConverter\DateTypeConverter());
    }

    /**
     * @param LoggerInterface $logger
     */
    public function withLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param EventDispatcherInterface $dispatcher
     */
    public function withEventDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param TypeConverterCollection $converters
     */
    public function withTypeConverter(TypeConverterCollection $converters)
    {
        $this->converters = $converters;
    }

    /**
     * @param ClassMapCollection $classMaps
     */
    public function withClassMaps(ClassMapCollection $classMaps)
    {
        $this->classMaps = $classMaps;
    }

    /**
     * @param ClassMapInterface $classMap
     */
    public function addClassMap(ClassMapInterface $classMap)
    {
        $this->classMaps->add($classMap);
    }

    /**
     * @param TypeConverterInterface $typeConverter
     */
    public function addTypeConverter(TypeConverterInterface $typeConverter)
    {
        $this->converters->add($typeConverter);
    }

    /**
     * @param HandlerInterface $handler
     */
    public function withHandler(HandlerInterface $handler)
    {
        $this->handler = $handler;
    }

    /**
     * @param MiddlewareInterface $middleware
     */
    public function addMiddleware(MiddlewareInterface $middleware)
    {
        $this->middlewares[] = $middleware;
    }

    /**
     * @return ClientInterface
     * @throws \Phpro\SoapClient\Exception\InvalidArgumentException
     */
    public function build()
    {
        $soapClientFactory = new SoapClientFactory($this->classMaps, $this->converters);
        $soapClient = $soapClientFactory->factory($this->wsdl, $this->soapOptions);

        if ($this->handler) {
            $soapClient->setHandler($this->handler);
        }

        if (count($this->middlewares)) {
            if (!$this->handler instanceof MiddlewareSupportingInterface) {
                throw new InvalidArgumentException('The SOAP handler you selected does not support middlewares.');
            }

            foreach ($this->middlewares as $middleware) {
                $this->handler->addMiddleware($middleware);
            }
        }

        if ($this->logger) {
            $this->dispatcher->addSubscriber(new LogPlugin($this->logger));
        }

        return $this->clientFactory->factory($soapClient, $this->dispatcher);
    }
}
