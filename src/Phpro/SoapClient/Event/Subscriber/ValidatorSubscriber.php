<?php

namespace Phpro\SoapClient\Event\Subscriber;

use Phpro\SoapClient\CodeGenerator\GeneratorInterface;
use Phpro\SoapClient\Event\RequestEvent;
use Phpro\SoapClient\Events;
use Phpro\SoapClient\Exception\RequestException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            Events::REQUEST => 'onClientRequest',
        ];
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
            throw new RequestException(self::toString($errors));
        }
    }

    private static function toString(ConstraintViolationListInterface $errors): string
    {
        $strErrors = [];
        /** @var ConstraintViolationInterface $error */
        foreach ($errors as $error) {
            $strErrors[] = $error->getMessage();
        }

        return implode(GeneratorInterface::EOL, $strErrors);
    }
}
