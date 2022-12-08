<?php

namespace Phpro\SoapClient\Console\Command;

use Phpro\SoapClient\CodeGenerator\ClassMapGenerator;
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
use function Psl\Type\non_empty_string;

/**
 * Class GenerateTypesCommand
 *
 * @package Phpro\SoapClient\Console\Command
 */
class GenerateClassmapCommand extends Command
{

    const COMMAND_NAME = 'generate:classmap';

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * GenerateClassmapCommand constructor.
     *
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
            ->setDescription('Generates a classmap based on WSDL.')
            ->addOption(
                'config',
                null,
                InputOption::VALUE_REQUIRED,
                'The location of the soap code-generator config file'
            );
    }

    /**
     * {@inheritdoc}
     * @throws \Phpro\SoapClient\Exception\InvalidArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $io = new SymfonyStyle($input, $output);

        $config = $this->getConfigHelper()->load($input);
        $typeMap = TypeMap::fromMetadata(
            non_empty_string()->assert($config->getTypeNamespace()),
            $config->getEngine()->getMetadata()->getTypes()
        );

        $generator = new ClassMapGenerator(
            $config->getRuleSet(),
            $config->getClassMapName(),
            $config->getClassMapNamespace()
        );
        $path = $config->getClassMapDestination().DIRECTORY_SEPARATOR.$config->getClassMapName().'.php';
        $this->handleClassmap($generator, $typeMap, $path);

        $io->success('Generated classmap at ' . $path);
        
        return 0;
    }


    /**
     * Generates one type class
     *
     * @param FileGenerator $file
     * @param ClassMapGenerator $generator
     * @param TypeMap $typeMap
     * @param string $path
     */
    protected function generateClassmap(
        FileGenerator $file,
        ClassMapGenerator $generator,
        TypeMap $typeMap,
        string $path
    ) {
        $code = $generator->generate($file, $typeMap);
        $this->filesystem->putFileContents($path, $code);
    }

    /**
     * Try to create a class for a type.
     *
     * @param ClassMapGenerator $generator
     * @param TypeMap           $typeMap
     * @param string            $path
     *
     * @return bool
     */
    protected function handleClassmap(ClassMapGenerator $generator, TypeMap $typeMap, string $path): bool
    {
        // Try to create a new class:
        try {
            $file = new FileGenerator();
            $this->generateClassmap($file, $generator, $typeMap, $path);
        } catch (\Exception $e) {
            $this->output->writeln('<fg=red>'.$e->getMessage().'</fg=red>');

            return false;
        }

        return true;
    }

    /**
     * Function for added type hint
     */
    public function getConfigHelper(): ConfigHelper
    {
        return instance_of(ConfigHelper::class)->assert($this->getHelper('config'));
    }
}
