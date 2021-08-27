<?php

declare(strict_types=1);

namespace PhproTest\SoapClient\Unit;

use Phpro\SoapClient\Client;
use Phpro\SoapClient\Event\RequestEvent;
use Phpro\SoapClient\Event\ResponseEvent;
use Phpro\SoapClient\Type\RequestInterface;
use Phpro\SoapClient\Type\ResultInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

class ResponseEventTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var Client & ObjectProphecy
     */
    private Client $client;

    /**
     * @var RequestInterface & ObjectProphecy
     */
    private RequestInterface $request;

    /**
     * @var ResultInterface & ObjectProphecy
     */
    private ResultInterface $response;

    private RequestEvent $requestEvent;
    private ResponseEvent $event;

    protected function setUp(): void
    {
        $this->client = $this->prophesize(Client::class)->reveal();
        $this->request = $this->prophesize(RequestInterface::class)->reveal();
        $this->response = $this->prophesize(ResultInterface::class)->reveal();
        $this->requestEvent = new RequestEvent($this->client, 'method', $this->request);

        $this->event = new ResponseEvent($this->client, $this->requestEvent, $this->response);
    }

    /** @test */
    public function it_contains_a_client(): void
    {
        self::assertSame($this->client, $this->event->getClient());
    }

    /** @test */
    public function it_contains_a_request_event(): void
    {
        self::assertSame($this->requestEvent, $this->event->getRequestEvent());
    }

    /** @test */
    public function it_contains_a_response(): void
    {
        self::assertSame($this->response, $this->event->getResponse());
    }

    /** @test */
    public function it_can_overwrite_response(): void
    {
        $new = $this->prophesize(ResultInterface::class)->reveal();
        $this->event->registerResponse($new);

        self::assertSame($new, $this->event->getResponse());
        self::assertNotSame($this->response, $this->event->getResponse());
    }
}
