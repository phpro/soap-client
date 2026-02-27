<?php

namespace Phpro\SoapClient\Console\Command;

use Phpro\SoapClient\CodeGenerator\ClientGenerator;
use Phpro\SoapClient\CodeGenerator\Model\Client;
use Phpro\SoapClient\CodeGenerator\Model\ClientMethodMap;
use Phpro\SoapClient\Console\Helper\ConfigHelper;
use Phpro\SoapClient\Util\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Laminas\Code\Generator\FileGenerator;
use function Psl\Type\instance_of;

class GenerateClientCommand extends Command
{
    const COMMAND_NAME = 'generate:client';

    public function __construct(
        private Filesystem $filesystem
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription('Generates a client based on WSDL.')
            ->addOption(
                'config',
                null,
                InputOption::VALUE_REQUIRED,
                'The location of the soap code-generator config file'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $config = $this->getConfigHelper()->load($input);

        $clientConfig = $config->getClient();
        $destination = $clientConfig->path();
        $methodMap = ClientMethodMap::fromMetadata(
            $config->getTypeNamespaceMap(),
            $config->getManipulatedMetadata()->getMethods(),
        );

        $client = new Client($clientConfig, $methodMap);
        $generator = new ClientGenerator($config->getRuleSet());

        $this->filesystem->putFileContents(
            $destination,
            $generator->generate(
                new FileGenerator(),
                $client,
            )
        );

        $io->success('Generated client at ' . $destination);

        return self::SUCCESS;
    }

    public function getConfigHelper(): ConfigHelper
    {
        return instance_of(ConfigHelper::class)->assert($this->getHelper('config'));
    }
}
