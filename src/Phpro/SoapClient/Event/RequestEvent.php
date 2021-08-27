<?php

namespace Phpro\SoapClient\Event;

use Phpro\SoapClient\Caller;
use Phpro\SoapClient\Type\RequestInterface;

class RequestEvent
{
    /**
     * @var string
     */
    protected $method;

    /**
     * @var RequestInterface
     */
    private $request;

    public function __construct(string $method, RequestInterface $request)
    {
        $this->method = $method;
        $this->request = $request;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return RequestInterface
     */
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    public function registerRequest(RequestInterface $request): void
    {
        $this->request = $request;
    }
}
