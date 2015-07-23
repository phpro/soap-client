<?php
namespace Phpro\SoapClient\Event;

use Phpro\SoapClient\Type\ResultInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class DebugEvent
 *
 * @package Phpro\SoapClient\Event
 */
class DebugEvent extends Event
{
    /**
     * @var RequestEvent
     */
    protected $requestEvent;

    /**
     * @var mixed
     */
    protected $response;

    /**
     * @var mixed
     */
    protected $debug;

    /**
     * @param RequestEvent $requestEvent
     * @param ResultInterface $response
     * @param $debug
     */
    public function __construct(RequestEvent $requestEvent, ResultInterface $response, $debug)
    {
        $this->requestEvent = $requestEvent;
        $this->response = $response;
        $this->debug = $debug;
    }

    /**
     * @return RequestEvent
     */
    public function getRequestEvent()
    {
        return $this->requestEvent;
    }

    /**
     * @return ResultInterface
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Output of client's debugLastSoapRequest
     * @return mixed
     */
    public function debugLastSoapRequest()
    {
        return $this->debug;
    }
}
