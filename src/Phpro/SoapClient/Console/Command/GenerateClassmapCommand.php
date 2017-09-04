<?php

namespace Phpro\SoapClient\Console\Command;

use Phpro\SoapClient\CodeGenerator\ClassMapGenerator;
use Phpro\SoapClient\CodeGenerator\Config\ConfigInterface;
use Phpro\SoapClient\CodeGenerator\Model\TypeMap;
use Phpro\SoapClient\Exception\InvalidArgumentException;
use Phpro\SoapClient\Soap\SoapClient;
use Phpro\SoapClient\Util\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Zend\Code\Generator\FileGenerator;

/**
 * Class GenerateTypesCommand
 *
 * @package Phpro\SoapClient\Console\Command
 */
class GenerateClassmapCommand extends Command
{

    const COMMAND_NAME = 'generate:classmap';

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * GenerateClassmapCommand constructor.
     *
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        parent::__construct();
        $this->filesystem = $filesystem;
    }

    /**
     * Configure the command.
     */
    protected function configure()
    {
        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription('Generates a classmap based on WSDL.')
            ->addOption(
                'config',
                null,
                InputOption::VALUE_REQUIRED,
                'The location of the soap code-generator config file'
            )
        ;
    }

    /**
     * {@inheritdoc}
     * @throws \Phpro\SoapClient\Exception\InvalidArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $configFile = $input->getOption('config');
        if (!$configFile || !$this->filesystem->fileExists($configFile)) {
            throw InvalidArgumentException::invalidConfigFile();
        }

        $config = include $configFile;
        if (!$config instanceof ConfigInterface) {
            throw InvalidArgumentException::invalidConfigFile();
        }

        if ($config->getGenerateClassmapCommandClassName() !== static::class) {
            $commandClassName = $config->getGenerateClassmapCommandClassName();
            if (!is_subclass_of($commandClassName, Command::class)) {
                throw InvalidArgumentException::invalidGenerateClassmapCommand();
            }
            $command = new $commandClassName($this->filesystem);
            $command->execute($input, $output);

            return;
        }

        $soapClient = $this->getSoapClient($config);
        $typeMap = TypeMap::fromSoapClient($config->getNamespace(), $soapClient);

        $file = $this->getFileGenerator($config);
        $generator = $this->getClassMapGenerator($config);
        $output->write($generator->generate($file, $typeMap));
    }


    /**
     * @param ConfigInterface $config
     * @return SoapClient
     */
    protected function getSoapClient(ConfigInterface $config)
    {
        return new SoapClient($config->getWsdl(), $config->getSoapOptions());
    }

    /**
     * @param ConfigInterface $config
     * @return FileGenerator
     */
    protected function getFileGenerator(ConfigInterface $config)
    {
        return new FileGenerator();
    }

    /**
     * @param ConfigInterface $config
     * @return ClassMapGenerator
     */
    protected function getClassMapGenerator(ConfigInterface $config)
    {
        return new ClassMapGenerator($config->getRuleSet());
    }
}
