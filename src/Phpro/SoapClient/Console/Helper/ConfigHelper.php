<?php

namespace Phpro\SoapClient\Console\Helper;

use Phpro\SoapClient\CodeGenerator\Config\Config;
use Phpro\SoapClient\CodeGenerator\Config\ConfigInterface;
use Phpro\SoapClient\Exception\InvalidArgumentException;
use Phpro\SoapClient\Util\Filesystem;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputInterface;

class ConfigHelper extends Helper
{
    public function getName():string
    {
        return 'config';
    }

    /**
     * Attempts to load the configuration file, returns it on success
     * @param InputInterface $input
     * @param Filesystem $filesystem
     * @return Config
     */
    public function load(InputInterface $input, Filesystem $filesystem): Config
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

    public function getHelperSet(): HelperSet
    {
        $set = new HelperSet();
        $set->set($this);
        return $set;
    }
}
