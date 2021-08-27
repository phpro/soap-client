<?php
namespace Phpro\SoapClient\Event;

use Phpro\SoapClient\Caller;
use Phpro\SoapClient\Type\ResultInterface;

class ResponseEvent
{
    /**
     * @var RequestEvent
     */
    protected $requestEvent;

    /**
     * @var ResultInterface
     */
    protected $response;

    public function __construct(RequestEvent $requestEvent, ResultInterface $response)
    {
        $this->requestEvent = $requestEvent;
        $this->response = $response;
    }

    /**
     * @return RequestEvent
     */
    public function getRequestEvent(): RequestEvent
    {
        return $this->requestEvent;
    }

    /**
     * @return ResultInterface
     */
    public function getResponse(): ResultInterface
    {
        return $this->response;
    }

    public function registerResponse(ResultInterface $response): void
    {
        $this->response = $response;
    }
}
