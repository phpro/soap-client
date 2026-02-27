<?php

namespace Phpro\SoapClient\Console\Command;

use Laminas\Code\Generator\ClassGenerator;
use Phpro\SoapClient\CodeGenerator\ClientFactoryGenerator;
use Phpro\SoapClient\CodeGenerator\Context\ClassMapContext;
use Phpro\SoapClient\CodeGenerator\Context\ClientContext;
use Phpro\SoapClient\CodeGenerator\Context\ClientFactoryContext;
use Phpro\SoapClient\CodeGenerator\Model\TypeMap;
use Phpro\SoapClient\Console\Helper\ConfigHelper;
use Phpro\SoapClient\Util\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Laminas\Code\Generator\FileGenerator;
use function Psl\Type\instance_of;

class GenerateClientFactoryCommand extends Command
{
    const COMMAND_NAME = 'generate:clientfactory';

    public function __construct(
        private Filesystem $filesystem
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription('Generates a client factory')
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
        $generatorContext = $config->getCodeGeneratorContext();
        $classmapContext = new ClassMapContext(
            new FileGenerator(),
            new TypeMap($generatorContext, []),
            $config->getClassMap(),
            $generatorContext,
        );
        $clientContext = new ClientContext(
            new ClassGenerator(),
            $config->getClient(),
        );
        $context = new ClientFactoryContext($clientContext, $classmapContext);
        $generator = new ClientFactoryGenerator();
        $dest = $config->getClient()->destination->path.DIRECTORY_SEPARATOR.$config->getClient()->name.'Factory.php';
        $this->filesystem->putFileContents($dest, $generator->generate(new FileGenerator(), $context));

        $io->success('Generated client factory at ' . $dest);

        return self::SUCCESS;
    }

    public function getConfigHelper(): ConfigHelper
    {
        return instance_of(ConfigHelper::class)->assert($this->getHelper('config'));
    }
}
