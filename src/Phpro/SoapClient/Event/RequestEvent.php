<?php

namespace Phpro\SoapClient\Event;

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
     * @var array
     */
    protected $params = array();

    /**
     * @param       $method
     * @param array $params
     */
    public function __construct($method, array $params = array())
    {
        $this->method = $method;
        $this->params = $params;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }
}

