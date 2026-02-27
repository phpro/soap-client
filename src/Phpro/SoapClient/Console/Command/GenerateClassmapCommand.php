<?php

namespace Phpro\SoapClient\Console\Command;

use Phpro\SoapClient\CodeGenerator\ClassMapGenerator;
use Phpro\SoapClient\CodeGenerator\Model\TypeMap;
use Phpro\SoapClient\Console\Helper\ConfigHelper;
use Phpro\SoapClient\Soap\Metadata\Manipulators\TypesManipulatorChain;
use Phpro\SoapClient\Util\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Laminas\Code\Generator\FileGenerator;
use function Psl\Type\instance_of;

class GenerateClassmapCommand extends Command
{
    const COMMAND_NAME = 'generate:classmap';

    public function __construct(
        private Filesystem $filesystem
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription('Generates a classmap based on WSDL.')
            ->addOption(
                'config',
                null,
                InputOption::VALUE_REQUIRED,
                'The location of the soap code-generator config file'
            );
    }

    /**
     * @throws \Phpro\SoapClient\Exception\InvalidArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $config = $this->getConfigHelper()->load($input);
        // For class-maps, we don't want to do anything with duplicate types:
        // All types should be listed with namespace and name, even if that means there will be a duplicate entry.
        $config->setDuplicateTypeIntersectStrategy(new TypesManipulatorChain());

        $typeMap = TypeMap::fromMetadata(
            $config->getTypeNamespaceMap(),
            $config->getManipulatedMetadata()->getTypes(),
        );

        $generator = new ClassMapGenerator(
            $config->getRuleSet(),
            $config->getClassMap(),
        );
        $path = $config->getClassMap()->path();
        $this->filesystem->putFileContents(
            $path,
            $generator->generate(new FileGenerator(), $typeMap)
        );

        $io->success('Generated classmap at ' . $path);

        return self::SUCCESS;
    }

    public function getConfigHelper(): ConfigHelper
    {
        return instance_of(ConfigHelper::class)->assert($this->getHelper('config'));
    }
}
