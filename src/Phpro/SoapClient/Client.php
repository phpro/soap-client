<?php

namespace Phpro\SoapClient;

use Phpro\SoapClient\Event;
use Phpro\SoapClient\Exception\SoapException;
use Phpro\SoapClient\Type\MixedResult;
use Phpro\SoapClient\Type\MultiArgumentRequestInterface;
use Phpro\SoapClient\Type\RequestInterface;
use Phpro\SoapClient\Type\ResultInterface;
use Phpro\SoapClient\Type\ResultProviderInterface;
use SoapClient;

use SoapHeader;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class Client
 *
 * @package Phpro\SoapClient
 */
class Client implements ClientInterface
{
    /**
     * @var SoapClient
     */
    protected $soapClient;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @param SoapClient      $soapClient
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(SoapClient $soapClient, EventDispatcherInterface $dispatcher)
    {
        $this->soapClient = $soapClient;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param SoapHeader|SoapHeader[] $soapHeaders
     * @return $this
     */
    public function applySoapHeaders($soapHeaders): self
    {
        $this->soapClient->__setSoapHeaders($soapHeaders);
        return $this;
    }

    /**
     * Make it possible to debug the last request.
     *
     * @return array
     */
    public function debugLastSoapRequest(): array
    {
        return [
            'request'  => [
                'headers' => $this->soapClient->__getLastRequestHeaders(),
                'body'    => $this->soapClient->__getLastRequest(),
            ],
            'response' => [
                'headers' => $this->soapClient->__getLastResponseHeaders(),
                'body'    => $this->soapClient->__getLastResponse(),
            ],
        ];
    }

    /**
     * @param string $location
     */
    public function changeSoapLocation(string $location)
    {
        $this->soapClient->__setLocation($location);
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
        $this->dispatcher->dispatch(Events::REQUEST, $requestEvent);

        try {
            $arguments = ($request instanceof MultiArgumentRequestInterface) ? $request->getArguments() : [$request];
            $result = call_user_func_array([$this->soapClient, $method], $arguments);

            if ($result instanceof ResultProviderInterface) {
                $result = $result->getResult();
            }

            if (!$result instanceof ResultInterface) {
                $result = new MixedResult($result);
            }
        } catch (\Exception $exception) {
            $soapException = SoapException::fromThrowable($exception);
            $this->dispatcher->dispatch(Events::FAULT, new Event\FaultEvent($this, $soapException, $requestEvent));
            throw $soapException;
        }

        $this->dispatcher->dispatch(Events::RESPONSE, new Event\ResponseEvent($this, $requestEvent, $result));
        return $result;
    }
}
