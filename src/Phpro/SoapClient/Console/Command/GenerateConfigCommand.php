<?php

namespace Phpro\SoapClient\Console\Command;

use Phpro\SoapClient\CodeGenerator\Config\ClassMapConfig;
use Phpro\SoapClient\CodeGenerator\Config\ClientConfig;
use Phpro\SoapClient\CodeGenerator\Config\Destination;
use Phpro\SoapClient\CodeGenerator\Config\TypeNamespaceMap;
use Phpro\SoapClient\CodeGenerator\ConfigGenerator;
use Phpro\SoapClient\CodeGenerator\Context\ConfigContext;
use Phpro\SoapClient\CodeGenerator\Util\Normalizer;
use Phpro\SoapClient\Console\Validator\NotBlankValidator;
use Phpro\SoapClient\Util\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Laminas\Code\Generator\FileGenerator;

class GenerateConfigCommand extends Command
{
    const COMMAND_NAME = 'generate:config';

    public function __construct(
        private Filesystem $filesystem
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription('Interactively generate basic configuration')
            ->addOption(
                'config',
                null,
                InputOption::VALUE_REQUIRED,
                'The location of the soap code-generator config file'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $context = new ConfigContext();
        $io = new SymfonyStyle($input, $output);
        $required = new NotBlankValidator();

        // Ask for config location:
        $destination = $input->getOption('config');
        if (!$destination) {
            $destination = $io->ask(
                'config location (Where to put the config, including .php)',
                'config/soap-client.php'
            );
        }

        $context->setWsdl($io->ask('Wsdl location (URL or path to file)', null, $required));
        $context->setGenerateDocblocks($io->confirm('Should methods be generated with docblocks?', true));
        $name = $io->ask(
            'Generic name used to name this client (Results in <name>Client <name>Classmap etc.)',
            null,
            $required
        );
        $baseDir = $io->ask('Directory where the client should be generated in', null, $required);
        $namespace = Normalizer::normalizeNamespace($io->ask('Namespace for your client', null, $required));

        $context->addSetter('setTypeNamespaceMap', sprintf(
            '%s::create(new %s(%s, %s))',
            '\\' . TypeNamespaceMap::class,
            '\\' . Destination::class,
            var_export($baseDir . DIRECTORY_SEPARATOR . 'Type', true),
            var_export($namespace . '\\Type', true)
        ));

        $context->addSetter('setClient', sprintf(
            'new %s(%s, new %s(%s, %s))',
            '\\' . ClientConfig::class,
            var_export($name.'Client', true),
            '\\' . Destination::class,
            var_export($baseDir, true),
            var_export($namespace, true)
        ));

        $context->addSetter('setClassMap', sprintf(
            'new %s(%s, new %s(%s, %s))',
            '\\' . ClassMapConfig::class,
            var_export($name.'Classmap', true),
            '\\' . Destination::class,
            var_export($baseDir, true),
            var_export($namespace, true)
        ));

        // Create the config
        $generator = new ConfigGenerator();
        $this->filesystem->putFileContents($destination, $generator->generate(new FileGenerator(), $context));
        $io->success('Config has been written to ' . $destination);

        return self::SUCCESS;
    }
}
