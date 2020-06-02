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
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Laminas\Code\Generator\FileGenerator;

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
     * @var InputInterface
     */
    private $input;

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
            )
            ->addOption('overwrite', 'o', InputOption::VALUE_NONE, 'Makes it possible to overwrite by default');
    }

    /**
     * {@inheritdoc}
     * @throws \Phpro\SoapClient\Exception\InvalidArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $io = new SymfonyStyle($input, $output);

        $config = $this->getConfigHelper()->load($input);
        $typeMap = TypeMap::fromMetadata(
            $config->getTypeNamespace(),
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
     * When a class exists: try to patch
     * If patching the old class does not work: ask for an overwrite
     * Create a class from an empty file
     *
     * @param ClassMapGenerator $generator
     * @param TypeMap           $typeMap
     * @param string            $path
     *
     * @return bool
     */
    protected function handleClassmap(ClassMapGenerator $generator, TypeMap $typeMap, string $path): bool
    {
        // Handle existing class:
        if ($this->filesystem->fileExists($path)) {
            if ($this->handleExistingFile($generator, $typeMap, $path)) {
                return true;
            }

            // Ask if a class can be overwritten if it contains errors
            if (!$this->askForOverwrite()) {
                $this->output->writeln(sprintf('Skipping %s', $path));

                return false;
            }
        }

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
     * An existing file was found. Try to patch or ask if it can be overwritten.
     *
     * @param ClassMapGenerator $generator
     * @param TypeMap           $typeMap
     * @param string            $path
     * @return bool
     */
    protected function handleExistingFile(ClassMapGenerator $generator, TypeMap $typeMap, $path): bool
    {
        $this->output->write(sprintf('Type %s exists. Trying to patch ...', $path));
        $patched = $this->patchExistingFile($generator, $typeMap, $path);

        if ($patched) {
            $this->output->writeln('Patched!');

            return true;
        }

        $this->output->writeln('Could not patch.');

        return false;
    }

    /**
     * This method tries to patch an existing type class.
     *
     * @param ClassMapGenerator $generator
     * @param TypeMap           $typeMap
     * @param string            $path
     * @return bool
     * @internal param Type $type
     */
    protected function patchExistingFile(ClassMapGenerator $generator, TypeMap $typeMap, $path): bool
    {
        try {
            $this->filesystem->createBackup($path);
            $file = FileGenerator::fromReflectedFileName($path);
            $this->generateClassmap($file, $generator, $typeMap, $path);
        } catch (\Exception $e) {
            $this->output->writeln('<fg=red>'.$e->getMessage().'</fg=red>');
            $this->filesystem->removeBackup($path);

            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    protected function askForOverwrite(): bool
    {
        $overwriteByDefault = $this->input->getOption('overwrite');
        $question = new ConfirmationQuestion('Do you want to overwrite it?', $overwriteByDefault);

        return $this->getHelper('question')->ask($this->input, $this->output, $question);
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
