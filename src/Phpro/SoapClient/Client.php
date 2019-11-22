<?php

namespace Phpro\SoapClient;

use Phpro\SoapClient\Event;
use Phpro\SoapClient\Exception\SoapException;
use Phpro\SoapClient\Soap\Engine\EngineInterface;
use Phpro\SoapClient\Type\MixedResult;
use Phpro\SoapClient\Type\MultiArgumentRequestInterface;
use Phpro\SoapClient\Type\RequestInterface;
use Phpro\SoapClient\Type\ResultInterface;
use Phpro\SoapClient\Type\ResultProviderInterface;
use Phpro\SoapClient\Util\XmlFormatter;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\Event as SymfonyEvent;

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
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    public function __construct(EngineInterface $engine, EventDispatcherInterface $dispatcher)
    {
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
     * @param string            $eventName
     * @param SymfonyEvent|null $event
     */
    private function dispatch(string $eventName, SymfonyEvent $event = null): void
    {
        if (interface_exists('\Symfony\Contracts\EventDispatcher\EventDispatcherInterface') && $this->dispatcher instanceof \Symfony\Contracts\EventDispatcher\EventDispatcherInterface) {
            $this->dispatcher->dispatch($event, $eventName);
        } else {
            $this->dispatcher->dispatch($eventName, $event);
        }
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
        $requestEvent = new Event\RequestEvent($this, $method, $request);

        $this->dispatch(Events::REQUEST, $requestEvent);

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
            $this->dispatch(Events::FAULT, new Event\FaultEvent($this, $soapException, $requestEvent));
            throw $soapException;
        }

        $this->dispatch(Events::RESPONSE, new Event\ResponseEvent($this, $requestEvent, $result));

        return $result;
    }
}
