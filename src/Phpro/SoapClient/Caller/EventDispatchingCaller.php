<?php
declare(strict_types=1);

namespace Phpro\SoapClient\Caller;

use Phpro\SoapClient\Event;
use Phpro\SoapClient\Exception\SoapException;
use Phpro\SoapClient\Type\RequestInterface;
use Phpro\SoapClient\Type\ResultInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

final class EventDispatchingCaller implements Caller
{
    private Caller $caller;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(Caller $caller, EventDispatcherInterface $eventDispatcher)
    {
        $this->caller = $caller;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(string $method, RequestInterface $request): ResultInterface
    {
        /** @var Event\RequestEvent $requestEvent */
        $requestEvent = $this->eventDispatcher->dispatch(new Event\RequestEvent($method, $request));
        $request = $requestEvent->getRequest();

        try {
            $result = ($this->caller)($method, $request);
        } catch (SoapException $exception) {
            $this->eventDispatcher->dispatch(new Event\FaultEvent($exception, $requestEvent));
            throw $exception;
        }

        /** @var Event\ResponseEvent $responseEvent */
        $responseEvent = $this->eventDispatcher->dispatch(new Event\ResponseEvent($requestEvent, $result));

        return $responseEvent->getResponse();
    }
}
