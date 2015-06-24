<?php

namespace Phpro\SoapClient\Console\Command;

use Phpro\SoapClient\Exception\RunTimeException;
use Phpro\SoapClient\CodeGenerator\Generator\TypeGenerator;
use Phpro\SoapClient\Soap\SoapClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class GenerateTypesCommand
 *
 * @package Phpro\SoapClient\Console\Command
 */
class GenerateTypesCommand extends Command
{

    const COMMAND_NAME = 'types:generate';

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
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fileSystem = new Filesystem();
        $destination = rtrim($input->getArgument('destination'), '/\\');
        if (!$fileSystem->exists($destination)) {
            throw new RunTimeException(sprintf('The destination %s does not exist.', $destination));
        }

        $wsdl = $input->getOption('wsdl');
        if (!$wsdl) {
            throw new RuntimeException('You MUST specify a WSDL endpoint.');
        }

        $namespace = $input->getOption('namespace');
        $soapClient = new SoapClient($wsdl, []);
        $types = $soapClient->getSoapTypes();

        $generator = new TypeGenerator($destination, $namespace);
        foreach ($types as $type => $properties) {
            $data = $generator->generate($type, $properties);
            $file = sprintf('%s/%s.php', $destination, ucfirst($type));
            file_put_contents($file, $data);
            $output->writeln(sprintf('Generated class %s to %s', $type, $file));
        }

        $output->writeln('Done');
    }
}
