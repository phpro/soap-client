<?php

namespace Phpro\SoapClient\Console\Command;

use Phpro\SoapClient\CodeGenerator\Assembler\PropertyAssembler;
use Phpro\SoapClient\CodeGenerator\Model\Type;
use Phpro\SoapClient\CodeGenerator\Model\TypeMap;
use Phpro\SoapClient\CodeGenerator\Rules\AssembleRule;
use Phpro\SoapClient\CodeGenerator\Rules\RuleSet;
use Phpro\SoapClient\CodeGenerator\TypeGenerator;
use Phpro\SoapClient\Exception\RunTimeException;
use Phpro\SoapClient\Soap\SoapClient;
use Phpro\SoapClient\Util\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\FileGenerator;

/**
 * Class GenerateTypesCommand
 *
 * @package Phpro\SoapClient\Console\Command
 */
class GenerateTypesCommand extends Command
{

    const COMMAND_NAME = 'generate:types';

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem = null)
    {
        parent::__construct(null);
        $this->filesystem = $filesystem ?: new Filesystem();
    }

    /**
     * Configure the command.
     */
    protected function configure()
    {
        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription('Generates types based on WSDL.')
            ->addArgument('destination', InputArgument::REQUIRED, 'Destination folder')
            ->addOption('wsdl', null, InputOption::VALUE_REQUIRED, 'The WSDL on which you base the types')
            ->addOption('namespace', null, InputOption::VALUE_OPTIONAL, 'Resulting namespace')
            ->addOption('overwrite', 'o', InputOption::VALUE_NONE, 'Makes it possible to overwrite by default')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $destination = rtrim($input->getArgument('destination'), '/\\');
        if (!$this->filesystem->dirextoryExists($destination)) {
            throw new RunTimeException(sprintf('The destination %s does not exist.', $destination));
        }

        $wsdl = $input->getOption('wsdl');
        if (!$wsdl) {
            throw new RunTimeException('You MUST specify a WSDL endpoint.');
        }

        $namespace = $input->getOption('namespace');
        $soapClient = new SoapClient($wsdl, []);
        $typeMap = TypeMap::fromSoapClient($namespace, $soapClient);
        $generator = new TypeGenerator(new RuleSet([
            new AssembleRule(new PropertyAssembler())
        ]));
        
        foreach ($typeMap->getTypes() as $type) {
            $path = $type->getPathname($destination);
            if ($this->handleType($input, $output, $generator, $type, $path)) {
                $output->writeln(sprintf('Generated class %s to %s', $type->getFullName(), $path));
            }
        }

        $output->writeln('Done');
    }

    /**
     * Try to create a class for a type.
     * When a class exists: try to patch
     * If patching the old class does not wor: ask for an overwrite
     * Create a class from an empty file
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param TypeGenerator   $generator
     * @param Type            $type
     * @param                 $path
     *
     * @return bool
     */
    protected function handleType(
        InputInterface $input,
        OutputInterface $output,
        TypeGenerator $generator,
        Type $type,
        $path
    ) {
        // Handle existing class:
        if ($this->filesystem->fileExists($path)) {
            if ($this->handleExistingFile($output, $generator, $type, $path)) {
                return true;
            }

            // Ask if a class can be overwritten if it contains errors
            if (!$this->askForOverwrite($input, $output)) {
                $output->writeln(sprintf('Skipping %s', $type->getName()));
                return false;
            }
        }

        // Try to create a blanco class:
        try {
            $file = new FileGenerator();
            $this->generateType($file, $generator, $type, $path);
        } catch (\Exception $e) {
            $output->writeln('<fg=red>' . $e->getMessage() . '</fg=red>');
            return false;
        }

        return true;
    }

    /**
     * An existing file was found. Try to patch or ask if it can be overwritten.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param TypeGenerator   $generator
     * @param Type            $type
     * @param string          $path
     *
     * @return bool
     */
    protected function handleExistingFile(
        OutputInterface $output,
        TypeGenerator $generator,
        Type $type,
        $path
    ) {
        $output->write(sprintf('Type %s exists. Trying to patch ...', $type->getName()));
        $patched = $this->patchExistingFile($output, $generator, $type, $path);

        if ($patched) {
            $output->writeln('Patched!');
            return true;
        }

        $output->writeln('Could not patch.');

        return false;
    }

    /**
     * This method tries to patch an existing type class.
     *
     * @param OutputInterface $output
     * @param TypeGenerator   $generator
     * @param Type            $type
     * @param string          $path
     *
     * @return bool
     */
    protected function patchExistingFile(OutputInterface $output, TypeGenerator $generator, Type $type, $path)
    {
        try {
            $this->filesystem->createBackup($path);
            $file = FileGenerator::fromReflectedFileName($path);
            $this->generateType($file, $generator, $type, $path);
        } catch (\Exception $e) {
            $output->writeln('<fg=red>' . $e->getMessage() . '</fg=red>');
            $this->filesystem->removeBackup($path);
            return false;
        }

        return true;
    }

    /**
     * Generates one type class
     *
     * @param FileGenerator $file
     * @param TypeGenerator $generator
     * @param Type          $type
     * @param string        $path
     */
    protected function generateType(FileGenerator $file, TypeGenerator $generator, Type $type, $path)
    {
        $code = $generator->generate($file, $type);
        $this->filesystem->putFileContents($path, $code);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return bool
     */
    protected function askForOverwrite(InputInterface $input, OutputInterface $output)
    {
        $overwriteByDefault = $input->getOption('overwrite');
        $question = new ConfirmationQuestion('Do you want to overwrite it?', $overwriteByDefault);

        return $this->getHelper('question')->ask($input, $output, $question);
    }
}
