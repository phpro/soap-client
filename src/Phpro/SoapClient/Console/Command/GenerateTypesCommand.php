<?php

namespace Phpro\SoapClient\Console\Command;

use Phpro\SoapClient\CodeGenerator\Config\Config;
use Phpro\SoapClient\CodeGenerator\EnumerationGenerator;
use Phpro\SoapClient\CodeGenerator\GeneratorInterface;
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

class GenerateTypesCommand extends Command
{
    const COMMAND_NAME = 'generate:types';

    private OutputInterface $output;

    protected function configure(): void
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

    public function __construct(
        private Filesystem $filesystem
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->output = $output;
        $io = new SymfonyStyle($input, $output);

        $config = $this->getConfigHelper()->load($input);
        $typeMap = TypeMap::fromMetadata(
            $config->getTypeNamespaceMap(),
            $config->getManipulatedMetadata()->getTypes(),
        );

        foreach ($typeMap->getTypes() as $type) {
            $fileInfo = $type->getFileInfo();
            if ($this->handleType($config, $type, $fileInfo)) {
                $this->output->writeln(
                    sprintf('Generated class %s to %s', $type->getFullName(), $fileInfo->getPathname())
                );
            }
        }

        $io->success('All SOAP types generated');

        return self::SUCCESS;
    }

    /**
     * @return GeneratorInterface<Type>|null
     */
    private function detectCodeGeneratorForType(Config $config, Type $type): ?GeneratorInterface
    {
        $isConsideredScalar = (new IsConsideredScalarType())($type->getMeta());

        return match (true) {
            $isConsideredScalar && $type->getMeta()->enums()->isSome() => new EnumerationGenerator(),
            !$isConsideredScalar => new TypeGenerator($config->getRuleSet()),
            default => null
        };
    }

    protected function handleType(Config $config, Type $type, SplFileInfo $fileInfo): bool
    {
        $generator = $this->detectCodeGeneratorForType($config, $type);

        // Skip generation of "simple" types without generator.
        if (!$generator) {
            if ($this->output->isVeryVerbose()) {
                $this->output->writeln('<fg=yellow>Skipped scalar type : '.$type->getFullName().'</fg=yellow>');
            }
            return false;
        }

        // Generate type sub folders if needed
        $this->filesystem->ensureDirectoryExists($fileInfo->getPath());

        // Try to create a blanco class:
        try {
            $code = $generator->generate(new FileGenerator(), $type);
            $this->filesystem->putFileContents($fileInfo->getPathname(), $code);
        } catch (\Exception $e) {
            $this->output->writeln('<fg=red>Error generating '.$type->getFullName().':'.$e->getMessage().'</fg=red>');
            if ($this->output->isVeryVerbose()) {
                $this->output->writeln('<fg=red>'.$e->getTraceAsString().'</fg=red>');
            }

            return false;
        }

        return true;
    }

    public function getConfigHelper(): ConfigHelper
    {
        return instance_of(ConfigHelper::class)->assert($this->getHelper('config'));
    }
}
