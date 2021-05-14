<?php

namespace Phpro\SoapClient;

use Phpro\SoapClient\Event;
use Phpro\SoapClient\Event\Dispatcher\EventDispatcherInterface;
use Phpro\SoapClient\Exception\RuntimeException;
use Phpro\SoapClient\Exception\SoapException;
use Phpro\SoapClient\Soap\Engine\EngineInterface;
use Phpro\SoapClient\Type\MixedResult;
use Phpro\SoapClient\Type\MultiArgumentRequestInterface;
use Phpro\SoapClient\Type\RequestInterface;
use Phpro\SoapClient\Type\ResultInterface;
use Phpro\SoapClient\Type\ResultProviderInterface;
use Phpro\SoapClient\Util\XmlFormatter;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as SymfonyEventDispatcher;

/**
 * Class Client
 *
 * @package Phpro\SoapClient
 */
class Client implements ClientInterface
{
    /**
     * @var EngineInterface
     */
    protected $engine;

    /**
     * @deprecated We will be using our own EventDispatcherInterface in v2.0 which is in line with Symfony 5 and PSR14.
     * @var SymfonyEventDispatcher|EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @deprecated : In v2.0, we will only support our internal EventDispatcherInterface.
     */
    public function __construct(EngineInterface $engine, $dispatcher)
    {
        assert(
            $dispatcher instanceof SymfonyEventDispatcher || $dispatcher instanceof EventDispatcherInterface,
            new RuntimeException(sprintf(
                'Expected event dispatcher to be of type %s or %s, got "%s".',
                SymfonyEventDispatcher::class,
                EventDispatcherInterface::class,
                get_class($dispatcher)
            ))
        );

        $this->engine = $engine;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Make it possible to debug the last request.
     *
     * @return array
     */
    public function debugLastSoapRequest(): array
    {
        $lastRequestInfo = $this->engine->collectLastRequestInfo();
        return [
            'request' => [
                'headers' => trim($lastRequestInfo->getLastRequestHeaders()),
                'body'    => XmlFormatter::format($lastRequestInfo->getLastRequest()),
            ],
            'response' => [
                'headers' => trim($lastRequestInfo->getLastResponseHeaders()),
                'body'    => XmlFormatter::format($lastRequestInfo->getLastResponse()),
            ]
        ];
    }

    /**
     * For backward compatibility with Symfony 4
     *
     * @deprecated : We will remove this method  in v2.0 in favour of injecting the internal dispatcher directly.
     *
     * @template T of Event\SoapEvent
     * @param T $event
     * @return T
     */
    private function dispatch(Event\SoapEvent $event, string $name = null): Event\SoapEvent
    {
        $dispatcher = $this->dispatcher instanceof EventDispatcherInterface
            ? $this->dispatcher
            : new Event\Dispatcher\SymfonyEventDispatcher($this->dispatcher);

        return $dispatcher->dispatch($event, $name);
    }

    /**
     * @param string            $method
     * @param RequestInterface  $request
     *
     * @return ResultInterface
     * @throws SoapException
     */
    protected function call(string $method, RequestInterface $request): ResultInterface
    {
        $requestEvent = $this->dispatch(new Event\RequestEvent($this, $method, $request), Events::REQUEST);
        $request = $requestEvent->getRequest();

        try {
            $arguments = ($request instanceof MultiArgumentRequestInterface) ? $request->getArguments() : [$request];
            $result = $this->engine->request($method, $arguments);

            if ($result instanceof ResultProviderInterface) {
                $result = $result->getResult();
            }

            if (!$result instanceof ResultInterface) {
                $result = new MixedResult($result);
            }
        } catch (\Exception $exception) {
            $soapException = SoapException::fromThrowable($exception);
            $this->dispatch(new Event\FaultEvent($this, $soapException, $requestEvent), Events::FAULT);
            throw $soapException;
        }

        $responseEvent = $this->dispatch(new Event\ResponseEvent($this, $requestEvent, $result), Events::RESPONSE);

        return $responseEvent->getResponse();
    }
}
