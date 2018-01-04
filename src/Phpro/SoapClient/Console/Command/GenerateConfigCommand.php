<?php

namespace Phpro\SoapClient\Console\Command;

use Phpro\SoapClient\CodeGenerator\ConfigGenerator;
use Phpro\SoapClient\CodeGenerator\Context\ConfigContext;
use Phpro\SoapClient\Util\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Zend\Code\Generator\FileGenerator;

class GenerateConfigCommand extends Command
{
    public const COMMAND_NAME = 'generate:config';

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * GenerateConfigCommand constructor.
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription('Interactively generate basic configuration');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $context = new ConfigContext();
        $io = new SymfonyStyle($input, $output);
        $io->section('Config settings');
        $dest = $io->ask('config location (Where to put the config, including .php)', 'config/soap-client.php');
        $context->addSetter('setWsdl', $io->ask('Wsdl location (URL or path to file)'));
        $this->typeConfig($io, $context);
        $this->clientConfig($io, $context);
        $this->classmapConfig($io, $context);
        $this->rulesetConfig($io, $context);

        $generator = new ConfigGenerator();
        $this->filesystem->putFileContents($dest, $generator->generate(new FileGenerator(), $context));
        $io->success("Config has been written to $dest");
    }

    protected function typeConfig(SymfonyStyle $io, ConfigContext $context)
    {
        $io->section('Type Configuration');
        $context->addSetter(
            'setTypeDestination',
            $io->ask('destination (location where files are generated)', 'src/App/Type')
        );
        $context->addSetter(
            'setTypeNamespace',
            $io->ask('namespace (namespace of all types)', 'App/Type')
        );
    }

    protected function clientConfig(SymfonyStyle $io, ConfigContext $context)
    {
        $io->section('Client Configuration');
        $this->addNonEmptySetter(
            $context,
            'setClientDestination',
            $io->ask('destination (location where the client file is put)', 'src/App/Client')
        );
        $this->addNonEmptySetter(
            $context,
            'setClientName',
            $io->ask('name (name of the client)', 'Client')
        );
        $this->addNonEmptySetter(
            $context,
            'setClientNamespace',
            $io->ask('namespace (namespace of the client)', 'App/Client')
        );
    }

    public function classmapConfig(SymfonyStyle $io, ConfigContext $context)
    {
        $io->section('Classmap Configuration');
        if (!$io->confirm('Do you want to configure the classmap?')) {
            return;
        }
        $this->addNonEmptySetter(
            $context,
            'setClassmapDestination',
            $io->ask('destination (location where the classmap is generated)', 'src/App/Classmap')
        );
        $this->addNonEmptySetter(
            $context,
            'setClassmapName',
            $io->ask('name (name of the classmap)', 'Classmap')
        );
        $this->addNonEmptySetter(
            $context,
            'setClassmapNamespace',
            $io->ask('namespace (namespace of the classmap)', 'App/Classmap')
        );
    }

    public function rulesetConfig(SymfonyStyle $io, ConfigContext $context)
    {
        $io->section('Ruleset Configuration');
        $requestKeyword = $io->ask('Keyword for matching request objects', 'Request');
        $context->setRequestRegex("/$requestKeyword$/i");
        $responseKeyword = $io->ask('Keyword for matching response objects', 'Response');
        $context->setResponseRegex("/$responseKeyword$/i");
    }

    private function addNonEmptySetter(ConfigContext $context, string $key, string $value)
    {
        if ($value === '') {
            return;
        }
        if (preg_match('/namespace$/i', $key)) {
            $value = str_replace('/', '\\\\', $value);
        }
        $context->addSetter($key, $value);
    }
}
