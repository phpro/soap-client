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

        // Common information
        $dest = $io->ask('config location (Where to put the config, including .php)', 'config/soap-client.php');
        $context->addSetter('setWsdl', $io->ask('Wsdl location (URL or path to file)'));
        $name = $io->ask('Name for this client');
        $baseDir = $io->ask('Directory where the client should be generated in');
        $namespace = $io->ask('Namespace for your client');

        // Type
        $context->addSetter('setTypeDestination', $baseDir.DIRECTORY_SEPARATOR.'Type');
        $context->addSetter('setTypeNamespace', $namespace.'\\Type');

        // Client
        $this->addNonEmptySetter($context, 'setClientDestination', $baseDir);
        $this->addNonEmptySetter($context, 'setClientName', $name.'Client');
        $this->addNonEmptySetter($context, 'setClientNamespace', $namespace);

        // Classmap
        $this->addNonEmptySetter($context, 'setClassMapDestination', $baseDir);
        $this->addNonEmptySetter($context, 'setClassMapName', $name.'Classmap');
        $this->addNonEmptySetter($context, 'setClassMapNamespace', $namespace);

        // Ruleset
        $requestKeyword = $io->ask('Keyword for matching request objects', 'Request');
        $context->setRequestRegex("/$requestKeyword$/i");
        $responseKeyword = $io->ask('Keyword for matching response objects', 'Response');
        $context->setResponseRegex("/$responseKeyword$/i");

        // Create the config
        $generator = new ConfigGenerator();
        $this->filesystem->putFileContents($dest, $generator->generate(new FileGenerator(), $context));
        $io->success("Config has been written to $dest");
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
