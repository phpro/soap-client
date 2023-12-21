<?php

namespace Phpro\SoapClient\Console\Command;

use Phpro\SoapClient\CodeGenerator\Model\Type;
use Phpro\SoapClient\CodeGenerator\Model\TypeMap;
use Phpro\SoapClient\CodeGenerator\TypeGenerator;
use Phpro\SoapClient\Console\Helper\ConfigHelper;
use Phpro\SoapClient\Util\Filesystem;
use Soap\WsdlReader\Metadata\Predicate\IsConsideredScalarType;
use SplFileInfo;
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
class GenerateTypesCommand extends Command
{

    const COMMAND_NAME = 'generate:types';

    /**
     * @var Filesystem
     */
    private $filesystem;

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
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->output = $output;
        $io = new SymfonyStyle($input, $output);

        $config = $this->getConfigHelper()->load($input);
        $typeMap = TypeMap::fromMetadata(
            non_empty_string()->assert($config->getTypeNamespace()),
            $config->getEngine()->getMetadata()->getTypes()
        );
        $generator = new TypeGenerator($config->getRuleSet());

        $typesDestination = non_empty_string()->assert($config->getTypeDestination());
        foreach ($typeMap->getTypes() as $type) {
            $fileInfo = $type->getFileInfo($typesDestination);
            if ($this->handleType($generator, $type, $fileInfo)) {
                $this->output->writeln(
                    sprintf('Generated class %s to %s', $type->getFullName(), $fileInfo->getPathname())
                );
            }
        }

        $io->success('All SOAP types generated');

        return 0;
    }

    /**
     * Try to create a class for a type.
     *
     * @param TypeGenerator $generator
     * @param Type $type
     * @param SplFileInfo $fileInfo
     * @return bool
     */
    protected function handleType(TypeGenerator $generator, Type $type, SplFileInfo $fileInfo): bool
    {
        // Skip generation of simple types.
        if ((new IsConsideredScalarType())($type->getMeta())) {
            if ($this->output->isVeryVerbose()) {
                $this->output->writeln('<fg=yellow>Skipped scalar type : '.$type->getFullName().'</fg=yellow>');
            }
            return false;
        }

        // Generate type sub folders if needed
        $this->filesystem->ensureDirectoryExists($fileInfo->getPath());

        // Try to create a blanco class:
        try {
            $file = new FileGenerator();
            $this->generateType($file, $generator, $type, $fileInfo);
        } catch (\Exception $e) {
            $this->output->writeln('<fg=red>Error generating '.$type->getFullName().':'.$e->getMessage().'</fg=red>');
            if ($this->output->isVeryVerbose()) {
                $this->output->writeln('<fg=red>'.$e->getTraceAsString().'</fg=red>');
            }

            return false;
        }

        return true;
    }

    /**
     * Generates one type class
     *
     * @param FileGenerator $file
     * @param TypeGenerator $generator
     * @param Type $type
     * @param SplFileInfo $fileInfo
     */
    protected function generateType(FileGenerator $file, TypeGenerator $generator, Type $type, SplFileInfo $fileInfo)
    {
        $code = $generator->generate($file, $type);
        $this->filesystem->putFileContents($fileInfo->getPathname(), $code);
    }

    /**
     * Function for added type hint
     */
    public function getConfigHelper(): ConfigHelper
    {
        return instance_of(ConfigHelper::class)->assert($this->getHelper('config'));
    }
}
