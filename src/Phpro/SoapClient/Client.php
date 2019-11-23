<?php

namespace Phpro\SoapClient;

use Phpro\SoapClient\Adapter\EventDispatcherAdapter;
use Phpro\SoapClient\Event;
use Phpro\SoapClient\Exception\SoapException;
use Phpro\SoapClient\Soap\Engine\EngineInterface;
use Phpro\SoapClient\Type\MixedResult;
use Phpro\SoapClient\Type\MultiArgumentRequestInterface;
use Phpro\SoapClient\Type\RequestInterface;
use Phpro\SoapClient\Type\ResultInterface;
use Phpro\SoapClient\Type\ResultProviderInterface;
use Phpro\SoapClient\Util\XmlFormatter;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

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

    public function __construct(EngineInterface $engine, $dispatcher)
    {
        $this->engine = $engine;
        $this->dispatcher = $dispatcher;

        // Backward compatibility
        if ($dispatcher instanceof \Symfony\Component\EventDispatcher\EventDispatcherInterface) {
            $this->dispatcher = new EventDispatcherAdapter($dispatcher);
        }
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
     * @param string            $method
     * @param RequestInterface  $request
     *
     * @return ResultInterface
     * @throws SoapException
     */
    protected function call(string $method, RequestInterface $request): ResultInterface
    {
        $requestEvent = new Event\RequestEvent($this, $method, $request);
        $this->dispatcher->dispatch($requestEvent, Events::REQUEST);

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
            $this->dispatcher->dispatch(new Event\FaultEvent($this, $soapException, $requestEvent), Events::FAULT);
            throw $soapException;
        }

        $this->dispatcher->dispatch(new Event\ResponseEvent($this, $requestEvent, $result), Events::RESPONSE);
        return $result;
    }
}
