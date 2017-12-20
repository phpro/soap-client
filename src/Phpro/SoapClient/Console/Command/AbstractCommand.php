<?php

namespace Phpro\SoapClient\Console\Command;

use Phpro\SoapClient\CodeGenerator\Config\Config;
use Phpro\SoapClient\CodeGenerator\Config\ConfigInterface;
use Phpro\SoapClient\Exception\InvalidArgumentException;
use Phpro\SoapClient\Util\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;

class AbstractCommand extends Command
{
    /**
     * Attempts to load the configuration file, returns it on success
     * @param InputInterface $input
     * @param Filesystem     $filesystem
     * @return Config
     */
    public function loadConfig(InputInterface $input, Filesystem $filesystem): Config
    {
        $configFile = $input->getOption('config');
        if (!$configFile || !$filesystem->fileExists($configFile)) {
            throw InvalidArgumentException::invalidConfigFile();
        }

        $config = include $configFile;
        if (!$config instanceof ConfigInterface) {
            throw InvalidArgumentException::invalidConfigFile();
        }
        if (!$config instanceof Config) {
            throw InvalidArgumentException::invalidConfigFile();
        }

        return $config;
    }
}
