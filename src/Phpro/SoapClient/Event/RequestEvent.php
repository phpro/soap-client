<?php

namespace Phpro\SoapClient\Event;

use Phpro\SoapClient\Type\Request\RequestInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class RequestEvent
 *
 * @package Phpro\SoapClient\Event
 */
class RequestEvent extends Event
{
    /**
     * @var string
     */
    protected $method;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @param string           $method
     * @param RequestInterface $request
     */
    public function __construct($method, RequestInterface $request)
    {
        $this->method = $method;
        $this->request = $request;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return RequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }
}

