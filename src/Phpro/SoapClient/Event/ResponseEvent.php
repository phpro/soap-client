<?php
namespace Phpro\SoapClient\Event;

use Phpro\SoapClient\Type\ResultInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class ResponseEvent
 *
 * @package Phpro\SoapClient\Event
 */
class ResponseEvent extends Event
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
     * @param RequestEvent $requestEvent
     * @param ResultInterface $response
     */
    public function __construct(RequestEvent $requestEvent, ResultInterface $response)
    {
        $this->requestEvent = $requestEvent;
        $this->response = $response;
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
}

