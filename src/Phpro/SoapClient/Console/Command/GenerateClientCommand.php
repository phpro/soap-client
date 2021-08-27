<?php

namespace Phpro\SoapClient\Console\Command;

use Phpro\SoapClient\CodeGenerator\ClientGenerator;
use Phpro\SoapClient\CodeGenerator\GeneratorInterface;
use Phpro\SoapClient\CodeGenerator\Model\Client;
use Phpro\SoapClient\CodeGenerator\Model\ClientMethodMap;
use Phpro\SoapClient\CodeGenerator\TypeGenerator;
use Phpro\SoapClient\Console\Helper\ConfigHelper;
use Phpro\SoapClient\Util\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Laminas\Code\Generator\FileGenerator;

/**
 * Class GenerateClientCommand
 *
 * @package Phpro\SoapClient\Console\Command
 */
class GenerateClientCommand extends Command
{

    const COMMAND_NAME = 'generate:client';

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var InputInterface
     */
    private $input;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
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
            ->setDescription('Generates a client based on WSDL.')
            ->addOption(
                'config',
                null,
                InputOption::VALUE_REQUIRED,
                'The location of the soap code-generator config file'
            )
            ->addOption('overwrite', 'o', InputOption::VALUE_NONE, 'Makes it possible to overwrite by default');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $io = new SymfonyStyle($input, $output);

        $config = $this->getConfigHelper()->load($input);

        $destination = $config->getClientDestination().'/'.$config->getClientName().'.php';
        $methodMap = ClientMethodMap::fromMetadata(
            $config->getTypeNamespace(),
            $config->getEngine()->getMetadata()->getMethods()
        );

        $client = new Client($config->getClientName(), $config->getClientNamespace(), $methodMap);
        $generator = new ClientGenerator($config->getRuleSet());
        $fileGenerator = new FileGenerator();
        $this->generateClient(
            $fileGenerator,
            $generator,
            $client,
            $destination
        );

        $io->success('Generated client at ' . $destination);
        
        return 0;
    }

    /**
     * Generates one type class
     *
     * @param FileGenerator $file
     * @param GeneratorInterface $generator
     * @param Client $client
     * @param string $path
     */
    protected function generateClient(FileGenerator $file, GeneratorInterface $generator, Client $client, string $path)
    {
        $code = $generator->generate($file, $client);
        $this->filesystem->putFileContents($path, $code);
    }

    /**
     * Try to create a class for a type.
     * When a class exists: try to patch
     * If patching the old class does not work: ask for an overwrite
     * Create a class from an empty file
     *
     * @param ClientGenerator $generator
     * @param Client $client
     * @param string $path
     * @return bool
     */
    protected function handleClient(ClientGenerator $generator, Client $client, string $path): bool
    {
        // Try to create a blanco class:
        try {
            $file = new FileGenerator();
            $this->generateClient($file, $generator, $client, $path);
        } catch (\Exception $e) {
            $this->output->writeln('<fg=red>'.$e->getMessage().'</fg=red>');

            return false;
        }

        return true;
    }

    /**
     * Function for added type hint
     * @return ConfigHelper
     */
    public function getConfigHelper(): ConfigHelper
    {
        return $this->getHelper('config');
    }
}
