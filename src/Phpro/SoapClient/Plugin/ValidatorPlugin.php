<?php

/*
 * This file is part of the Phpro application.
 *
 * Copyright (c) 2015-2017 Phpro
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phpro\SoapClient\Plugin;

use Phpro\SoapClient\Event\RequestEvent;
use Phpro\SoapClient\Events;
use Phpro\SoapClient\Exception\RequestException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class ValidatorPlugin
 *
 * @package Phpro\SoapClient\Plugin
 */
class ValidatorPlugin implements EventSubscriberInterface
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
    public static function getSubscribedEvents()
    {
        return [
            Events::REQUEST  => 'onClientRequest',
        ];
    }
}
