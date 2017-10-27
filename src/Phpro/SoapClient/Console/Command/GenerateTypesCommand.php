<?php

/*
 * This file is part of the Phpro application.
 *
 * Copyright (c) 2015-2017 Phpro
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phpro\SoapClient\Console\Command;

use Phpro\SoapClient\CodeGenerator\Config\ConfigInterface;
use Phpro\SoapClient\CodeGenerator\Model\Type;
use Phpro\SoapClient\CodeGenerator\Model\TypeMap;
use Phpro\SoapClient\CodeGenerator\TypeGenerator;
use Phpro\SoapClient\Exception\InvalidArgumentException;
use Phpro\SoapClient\Soap\SoapClient;
use Phpro\SoapClient\Util\Filesystem;
use SplFileInfo;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
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
        parent::__construct(null);
        $this->filesystem = $filesystem;
    }

    /**
     * Configure the command.
     */
    protected function configure()
    {
        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription('Generates types based on WSDL.')
            ->addOption(
                'config',
                null,
                InputOption::VALUE_REQUIRED,
                'The location of the soap code-generator config file'
            )
            ->addOption('overwrite', 'o', InputOption::VALUE_NONE, 'Makes it possible to overwrite by default')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;

        $configFile = $this->input->getOption('config');
        if (!$configFile || !$this->filesystem->fileExists($configFile)) {
            throw InvalidArgumentException::invalidConfigFile();
        }

        $config = include $configFile;
        if (!$config instanceof ConfigInterface) {
            throw InvalidArgumentException::invalidConfigFile();
        }

        $soapClient = new SoapClient($config->getWsdl(), $config->getSoapOptions());
        $typeMap = TypeMap::fromSoapClient($config->getNamespace(), $soapClient);
        $generator = new TypeGenerator($config->getRuleSet());

        foreach ($typeMap->getTypes() as $type) {
            $fileInfo = $type->getFileInfo($config->getDestination());
            if ($this->handleType($generator, $type, $fileInfo)) {
                $this->output->writeln(
                    sprintf('Generated class %s to %s', $type->getFullName(), $fileInfo->getPathname())
                );
            }
        }

        $this->output->writeln('Done');
    }

    /**
     * Try to create a class for a type.
     * When a class exists: try to patch
     * If patching the old class does not wor: ask for an overwrite
     * Create a class from an empty file
     *
     * @param TypeGenerator $generator
     * @param Type          $type
     * @param SplFileInfo   $fileInfo
     * @return bool
     */
    protected function handleType(TypeGenerator $generator, Type $type, SplFileInfo $fileInfo)
    {
        // Generate type sub folders if needed
        $this->filesystem->ensureDirectoryExists($fileInfo->getPath());
        // Handle existing class:
        if ($fileInfo->isFile()) {
            if ($this->handleExistingFile($generator, $type, $fileInfo)) {
                return true;
            }

            // Ask if a class can be overwritten if it contains errors
            if (!$this->askForOverwrite()) {
                $this->output->writeln(sprintf('Skipping %s', $type->getName()));
                return false;
            }
        }

        // Try to create a blanco class:
        try {
            $file = new FileGenerator();
            $this->generateType($file, $generator, $type, $fileInfo);
        } catch (\Exception $e) {
            $this->output->writeln('<fg=red>'.$e->getMessage().'</fg=red>');

            return false;
        }

        return true;
    }

    /**
     * An existing file was found. Try to patch or ask if it can be overwritten.
     *
     * @param TypeGenerator $generator
     * @param Type          $type
     * @param SplFileInfo   $fileInfo
     * @return bool
     *
     */
    protected function handleExistingFile(TypeGenerator $generator, Type $type, SplFileInfo $fileInfo)
    {
        $this->output->write(sprintf('Type %s exists. Trying to patch ...', $type->getName()));
        $patched = $this->patchExistingFile($generator, $type, $fileInfo);

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
     * @param TypeGenerator $generator
     * @param Type          $type
     * @param SplFileInfo   $fileInfo
     * @return bool
     */
    protected function patchExistingFile(TypeGenerator $generator, Type $type, SplFileInfo $fileInfo)
    {
        try {
            $this->filesystem->createBackup($fileInfo->getPathname());
            $file = FileGenerator::fromReflectedFileName($fileInfo->getPathname());
            $this->generateType($file, $generator, $type, $fileInfo);
        } catch (\Exception $e) {
            $this->output->writeln('<fg=red>' . $e->getMessage() . '</fg=red>');
            $this->filesystem->removeBackup($fileInfo->getPathname());
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
     * @param SplFileInfo   $fileInfo
     */
    protected function generateType(FileGenerator $file, TypeGenerator $generator, Type $type, SplFileInfo $fileInfo)
    {
        $code = $generator->generate($file, $type);
        $this->filesystem->putFileContents($fileInfo->getPathname(), $code);
    }

    /**
     * @return bool
     */
    protected function askForOverwrite()
    {
        $overwriteByDefault = $this->input->getOption('overwrite');
        $question = new ConfirmationQuestion('Do you want to overwrite it?', $overwriteByDefault);

        return $this->getHelper('question')->ask($this->input, $this->output, $question);
    }
}
