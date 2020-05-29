<?php

namespace Phpro\SoapClient\Console\Command;

use Phpro\SoapClient\CodeGenerator\ConfigGenerator;
use Phpro\SoapClient\CodeGenerator\Context\ConfigContext;
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

    const RULE_CONFIRMATION = <<<CONFIRMATION
This tool can set some basic code generation rules. This requires some knowledge of the SOAP service.
Take a look at the message section in the WSDL. Are you able to match request and response elements based on keywords?
These keywords are used in a case insensitive regex match with '/' delimiters, escape accordingly!
CONFIRMATION;

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

    protected function configure()
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

    protected function execute(InputInterface $input, OutputInterface $output)
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
        $name = $io->ask(
            'Generic name used to name this client (Results in <name>Client <name>Classmap etc.)',
            null,
            $required
        );
        $baseDir = $io->ask('Directory where the client should be generated in', null, $required);
        $namespace = $io->ask('Namespace for your client', null, $required);

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
        if ($io->confirm(self::RULE_CONFIRMATION, false)) {
            $requestKeyword = $io->ask('Keyword for matching request objects', '');
            $context->setRequestRegex("/$requestKeyword/i");
            $responseKeyword = $io->ask('Keyword for matching response objects', '');
            $context->setResponseRegex("/$responseKeyword/i");
        }

        // Create the config
        $generator = new ConfigGenerator();
        $this->filesystem->putFileContents($destination, $generator->generate(new FileGenerator(), $context));
        $io->success('Config has been written to ' . $destination);

        return 0;
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
