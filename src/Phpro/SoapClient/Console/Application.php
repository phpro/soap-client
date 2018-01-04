<?php


namespace Phpro\SoapClient\Console;

use Phpro\SoapClient\Console\Command;
use Phpro\SoapClient\Console\Helper\ConfigHelper;
use Phpro\SoapClient\Util\Filesystem;
use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Console\Helper\HelperSet;

/**
 * Class Application
 *
 * @package Phpro\SoapClient\Console
 */
class Application extends SymfonyApplication
{
    public const APP_NAME = 'SoapClient';
    public const APP_VERSION = '0.1.0';

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
    protected function getDefaultCommands(): array
    {
        $filesystem = new Filesystem();
        $commands = parent::getDefaultCommands();
        $commands[] = new Command\GenerateTypesCommand($filesystem);
        $commands[] = new Command\GenerateClassmapCommand($filesystem);
        $commands[] = new Command\GenerateClientCommand($filesystem);
        $commands[] = new Command\GenerateClientFactoryCommand($filesystem);

        return $commands;
    }

    protected function setHelpers(Filesystem $filesystem)
    {
        $configHelper = new ConfigHelper($filesystem);
        $set = new HelperSet();
        $set->set($configHelper);
        $this->setHelperSet($set);
    }

    protected function getDefaultHelperSet(): HelperSet
    {
        $set = parent::getDefaultHelperSet();
        $set->set(new ConfigHelper(new Filesystem()));

        return $set;
    }
}
