<?php
namespace Phpro\SoapClient\Event;

use Phpro\SoapClient\Client;
use Phpro\SoapClient\Type\ResultInterface;

/**
 * Class ResponseEvent
 *
 * @package Phpro\SoapClient\Event
 */
class ResponseEvent extends SoapEvent
{
    /**
     * @var RequestEvent
     */
    protected $requestEvent;

    /**
     * @var ResultInterface
     */
    protected $response;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @param Client          $client
     * @param RequestEvent    $requestEvent
     * @param ResultInterface $response
     */
    public function __construct(Client $client, RequestEvent $requestEvent, ResultInterface $response)
    {
        $this->client = $client;
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

    /**
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    public function registerResponse(ResultInterface $response): void
    {
        $this->response = $response;
    }
}
