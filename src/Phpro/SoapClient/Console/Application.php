<?php


namespace Phpro\SoapClient\Console;

use Phpro\SoapClient\Console\Command;
use Phpro\SoapClient\Util\Filesystem;
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
        $filesystem = new Filesystem();
        $commands = parent::getDefaultCommands();
        $commands[] = new Command\GenerateTypesCommand($filesystem);
        $commands[] = new Command\GenerateClassmapCommand($filesystem);
        $commands[] = new Command\GenerateClientCommand($filesystem);

        return $commands;
    }
}
