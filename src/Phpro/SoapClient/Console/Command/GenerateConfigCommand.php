<?php

namespace Phpro\SoapClient\Console\Command;

use Phpro\SoapClient\Util\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Zend\Code\Generator\FileGenerator;
use Phpro\SoapClient\CodeGenerator\Config\Config;

class GenerateConfigCommand extends Command
{
    public const COMMAND_NAME = 'generate:config';

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var FileGenerator
     */
    private $file;

    /**
     * @var string
     */
    private $body = "return Config::create()\n";

    /**
     * GenerateConfigCommand constructor.
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
        parent::__construct();
    }

    protected function addSetter($name, $value, $namespace = false)
    {
        if ($value === '') {
            return;
        }
        if ($namespace) {
            $value = str_replace('/', '\\', $value);
        }
        $this->body .= sprintf("\t->%s('%s')".PHP_EOL, $name, addslashes($value));
    }

    protected function configure(): void
    {
        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription('Interactively generate basic configuration');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->file = new FileGenerator();
        $this->file->setUse('Phpro\\SoapClient\\CodeGenerator\\Assembler');
        $this->file->setUse('Phpro\\SoapClient\\CodeGenerator\\Rules');
        $this->file->setUse(Config::class);
        $io = new SymfonyStyle($input, $output);

        $io->section('Config settings');
        $dest = $io->ask('config location (Where to put the config, including .php)');
        $this->addSetter('setWsdl', $io->ask('Wsdl location (URL or path to file)'));
        $this->typeConfig($io);
        $this->clientConfig($io);
        $this->classmapConfig($io);

        $this->file->setBody($this->body.';');
        $this->filesystem->putFileContents($dest, $this->file->generate());
    }

    protected function typeConfig(SymfonyStyle $io)
    {
        $io->section('Type Configuration');
        if (!$io->confirm('Do you want to configure the types?')) {
            return;
        }
        $this->addSetter('setTypeDestination', $io->ask('destination (location where files are generated)'));
        $this->addSetter('setTypeNamespace', $io->ask('namespace (namespace of all types)'), true);
    }

    protected function clientConfig(SymfonyStyle $io)
    {
        $io->section('Client Configuration');
        if (!$io->confirm('Do you want to configure the client?')) {
            return;
        }
        $this->addSetter('setClientDestination', $io->ask('destination (location where the client file is put)'));
        $this->addSetter('setClientName', $io->ask('name (name of the client)'));
        $this->addSetter('setClientNamespace', $io->ask('namespace (namespace of the client)'), true);
    }

    public function classmapConfig(SymfonyStyle $io)
    {
        $io->section('Classmap Configuration');
        if (!$io->confirm('Do you want to configure the classmap?')) {
            return;
        }
        $this->addSetter('setClientDestination', $io->ask('destination (location where the classmap is generated)'));
        $this->addSetter('setClientName', $io->ask('name (name of the classmap)'));
        $this->addSetter('setClientNamespace', $io->ask('namespace (namespace of the classmap)'), true);
    }
}
