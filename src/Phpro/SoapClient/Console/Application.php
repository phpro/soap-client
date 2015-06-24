<?php


namespace Phpro\SoapClient\Console;

use Phpro\SoapClient\Console\Command\GenerateTypesCommand;
use Symfony\Component\Console\Application as SymfonyApplication;

/**
 * Class Application
 *
 * @package Phpro\SoapClient\Console
 */
class Application extends SymfonyApplication
{
    const APP_NAME = 'SoapClient';
    const APP_VERSION = '0.1.0';

    /**
     * @var ContainerBuilder
     */
    protected $container;

    /**
     * Set up application:
     */
    public function __construct()
    {
        parent::__construct(self::APP_NAME, self::APP_VERSION);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultCommands()
    {
        $commands = parent::getDefaultCommands();
        $commands[] = new GenerateTypesCommand();

        return $commands;
    }
}