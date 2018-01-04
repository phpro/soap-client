<?php

namespace Phpro\SoapClient\Console\Command;

use Phpro\SoapClient\CodeGenerator\ClientFactoryGenerator;
use Phpro\SoapClient\CodeGenerator\Context\ClientFactoryContext;
use Phpro\SoapClient\Util\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Zend\Code\Generator\FileGenerator;

class GenerateClientFactoryCommand extends Command
{
    public const COMMAND_NAME = 'generate:clientfactory';

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * GenerateClientBuilderCommand constructor.
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
        parent::__construct();
    }

    /**
     * Configure the command.
     */
    protected function configure()
    {
        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription('Generates a client factory')
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
        $io = new SymfonyStyle($input, $output);
        $config = $this->getHelper('config')->load($input, $this->filesystem);
        $context = ClientFactoryContext::fromConfig($config);
        $generator = new ClientFactoryGenerator();
        $dest = $config->getClientDestination().DIRECTORY_SEPARATOR.$config->getClientName().'Factory.php';
        $this->filesystem->putFileContents($dest, $generator->generate(new FileGenerator(), $context));
        $io->success("Generated a client factory at $dest");
    }
}
