<?php

namespace Phpro\SoapClient\Console\Command;

use Phpro\SoapClient\CodeGenerator\Config\ClassMapConfig;
use Phpro\SoapClient\CodeGenerator\Config\ClientConfig;
use Phpro\SoapClient\CodeGenerator\Config\Destination;
use Phpro\SoapClient\CodeGenerator\ConfigGenerator;
use Phpro\SoapClient\CodeGenerator\Context\ConfigContext;
use Phpro\SoapClient\CodeGenerator\Util\Normalizer;
use Phpro\SoapClient\Console\Validator\NotBlankValidator;
use Phpro\SoapClient\Soap\EngineOptions;
use Phpro\SoapClient\Util\Filesystem;
use Soap\WsdlReader\Model\Wsdl1;
use Soap\WsdlReader\Wsdl1Reader;
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

        $wsdlUri = $io->ask('Wsdl location (URL or path to file)', null, $required);
        $context->setWsdl($wsdlUri);

        $io->warning('Attempting to load WSDL... (this might take a while)');

        try {
            $wsdl = $this->loadWsdl($wsdlUri);
        } catch (\Throwable $e) {
            $io->warning('Could not load the provided WSDL with default engine options.');
            if ($output->isVerbose()) {
                $io->text('<fg=red>'.$e::class.'</>');
                $io->text($e->getMessage());
            }
            if ($output->isVeryVerbose()) {
                $io->text($e->getTraceAsString());
            }
            $io->info('Continuing generating configuration...');
            $wsdl = null;
        }


        $context->setDetectedXmlNamespaces($wsdl?->namespaces->namespaceToNameMap ?? []);
        $context->setGenerateDocblocks($io->confirm('Should methods be generated with docblocks?', true));
        $name = $io->ask(
            'Generic name used to name this client (Results in <name>Client <name>Classmap etc.)',
            null,
            $required
        );
        $baseDir = $io->ask('Directory where the client should be generated in', null, $required);
        $namespace = Normalizer::normalizeNamespace($io->ask('Namespace for your client', null, $required));

        // Create configuration objects
        $typeDestination = new Destination($baseDir . DIRECTORY_SEPARATOR . 'Type', $namespace . '\\Type');
        $context->setTypeDestination($typeDestination);

        $clientDestination = new Destination($baseDir, $namespace);
        $clientConfig = new ClientConfig($name . 'Client', $clientDestination);
        $context->setClientConfig($clientConfig);

        $classMapConfig = new ClassMapConfig($name . 'Classmap', $clientDestination);
        $context->setClassMapConfig($classMapConfig);

        // Create the config
        $generator = new ConfigGenerator();
        $this->filesystem->putFileContents($destination, $generator->generate(new FileGenerator(), $context));
        $io->success('Config has been written to ' . $destination);

        if (!$wsdl) {
            $io->warning(
                'The WSDL could not be loaded with default options.' .
                'You may need to configure custom engine options or verify the WSDL file manually before continuing.'
            );

            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    private function loadWsdl(string $wsdl): Wsdl1
    {
            $options = EngineOptions::defaults($wsdl);
            $loader = $options->getWsdlLoader();

            return (new Wsdl1Reader($loader))($wsdl);
    }
}
