<?php

namespace Phpro\SoapClient\Console\Event\Subscriber;

use Phpro\SoapClient\CodeGenerator\Util\Validator;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Check if zend code is installed when generating code
 * Show a helpful error message for when it is not
 *
 * Class ZendCodeValidationListener
 * @package Phpro\SoapClient\Event\Subscriber
 */
class ZendCodeValidationSubscriber implements EventSubscriberInterface
{
    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ConsoleEvents::COMMAND => 'onCommand',
        ];
    }

    public function onCommand(ConsoleCommandEvent $event)
    {
        if (!Validator::commandRequiresZendCode($event->getCommand()->getName())) {
            return;
        }
        if (Validator::zendCodeIsInstalled()) {
            return;
        }
        $io = new SymfonyStyle($event->getInput(), $event->getOutput());
        $io->error(
            [
                'zend-code not installed, require it with this command:',
                'composer require --dev zendframework/zend-code:^3.1.0',
            ]
        );
        $event->disableCommand();
    }
}
