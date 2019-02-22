<?php

namespace Phpro\SoapClient\Event\Subscriber;

use Phpro\SoapClient\Event\RequestEvent;
use Phpro\SoapClient\Events;
use Phpro\SoapClient\Exception\RequestException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class ValidatorSubscriber
 *
 * @package Phpro\SoapClient\Plugin
 */
class ValidatorSubscriber implements EventSubscriberInterface
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * Constructor
     *
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param RequestEvent $event
     *
     * @throws \Phpro\SoapClient\Exception\RequestException
     */
    public function onClientRequest(RequestEvent $event)
    {
        $errors = $this->validator->validate($event->getRequest());

        if (count($errors)) {
            throw new RequestException((string) $errors);
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            Events::REQUEST  => 'onClientRequest',
        ];
    }
}
