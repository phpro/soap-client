<?php

namespace Phpro\SoapClient\Console\Command;

use Phpro\SoapClient\CodeGenerator\Patcher;
use Phpro\SoapClient\Exception\PatchException;
use Phpro\SoapClient\Exception\RunTimeException;
use Phpro\SoapClient\CodeGenerator\Generator\TypeGenerator;
use Phpro\SoapClient\Soap\SoapClient;
use Phpro\SoapClient\Util\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

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
            throw new RuntimeException('You MUST specify a WSDL endpoint.');
        }

        $namespace = $input->getOption('namespace');
        $soapClient = new SoapClient($wsdl, []);
        $types = $soapClient->getSoapTypes();

        $generator = new TypeGenerator($namespace);
        foreach ($types as $type => $properties) {
            // Check if file exists:
            $file = sprintf('%s/%s.php', $destination, ucfirst($type));
            $data = $generator->generate($type, $properties);

            // Existing files ...
            if ($this->filesystem->fileExists($file)) {
                $this->handleExistingFile($input, $output, $file, $type, $data);
                continue;
            }

            // New files...
            $this->filesystem->putFileContents($file, $data);
            $output->writeln(sprintf('Generated class %s to %s', $type, $file));
        }

        $output->writeln('Done');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param string          $file
     * @param string          $type
     * @param string          $newContent
     */
    protected function handleExistingFile(InputInterface $input, OutputInterface $output, $file, $type, $newContent)
    {
        $output->write(sprintf('Type %s exists. Trying to patch ...', $type));

        // Patch the file
        $patched = $this->patchExistingFile($output, $file, $newContent);
        if ($patched) {
            $output->writeln('Patched!');
            return;
        }
        $output->writeln('Could not patch.');

        // Ask for overwriting the file:
        $allowOverwrite = $this->askForOverwrite($input, $output, $newContent);
        if (!$allowOverwrite) {
            $output->writeln(sprintf('Skipping %s', $type));
            return;
        }

        // Overwrite
        $this->filesystem->putFileContents($file, $newContent);
    }

    /**
     * @param OutputInterface $output
     * @param                 $file
     * @param                 $newContent
     *
     * @return bool
     */
    protected function patchExistingFile(OutputInterface $output, $file, $newContent)
    {
        $patcher = new Patcher($this->filesystem);
        try {
            $patcher->patch($file, $newContent);
        } catch (PatchException $e) {
            $output->writeln('<fg=red>' . $e->getMessage() . '</fg=red>');
            return false;
        }

        return true;
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
