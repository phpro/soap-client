<?php

namespace Phpro\SoapClient;

use Phpro\SoapClient\Event;
use Phpro\SoapClient\Type\RequestInterface;
use Phpro\SoapClient\Type\ResultInterface;
use Phpro\SoapClient\Type\ResultProviderInterface;
use SoapClient;
use SoapFault;
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
    public function applySoapHeaders($soapHeaders)
    {
        $this->soapClient->__setSoapHeaders($soapHeaders);
        return $this;
    }

    /**
     * Make it possible to debug the last request.
     *
     * @return array
     */
    public function debugLastSoapRequest()
    {
        return [
            'request' => [
                'headers' => $this->soapClient->__getLastRequestHeaders(),
                'body' => $this->soapClient->__getLastRequest(),
            ],
            'response' => [
               'headers' => $this->soapClient->__getLastResponseHeaders(),
                'body' => $this->soapClient->__getLastResponse(),
            ],
        ];
    }

    /**
     * @param string $location
     */
    public function changeSoapLocation($location)
    {
        $this->soapClient->__setLocation($location);
    }

    /**
     * @param string            $method
     * @param RequestInterface  $params
     *
     * @return ResultInterface
     * @throws SoapFault
     */
    protected function call($method, RequestInterface $request)
    {
        $requestEvent = new Event\RequestEvent($this, $method, $request);
        $this->dispatcher->dispatch(Events::REQUEST, $requestEvent);

        try {
            $result = $this->soapClient->$method($request);
            if ($result instanceof ResultProviderInterface) {
                $result = $result->getResult();
            }
        } catch (SoapFault $soapFault) {
            $this->dispatcher->dispatch(Events::FAULT, new Event\FaultEvent($this, $soapFault, $requestEvent));
            throw $soapFault;
        }

        $this->dispatcher->dispatch(Events::RESPONSE, new Event\ResponseEvent($this, $requestEvent, $result));
        return $result;
    }
}
