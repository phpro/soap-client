<?php

namespace Phpro\SoapClient\Console\Command;

use Phpro\SoapClient\CodeGenerator\Assembler\ClassMapAssembler;
use Phpro\SoapClient\CodeGenerator\ClassMapGenerator;
use Phpro\SoapClient\CodeGenerator\Model\TypeMap;
use Phpro\SoapClient\CodeGenerator\Rules\AssembleRule;
use Phpro\SoapClient\CodeGenerator\Rules\RuleSet;
use Phpro\SoapClient\Exception\RunTimeException;
use Phpro\SoapClient\Soap\SoapClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Zend\Code\Generator\FileGenerator;

/**
 * Class GenerateTypesCommand
 *
 * @package Phpro\SoapClient\Console\Command
 */
class GenerateClassmapCommand extends Command
{

    const COMMAND_NAME = 'generate:classmap';

    /**
     * Configure the command.
     */
    protected function configure()
    {
        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription('Generates classmap based on WSDL.')
            ->addOption('wsdl', null, InputOption::VALUE_REQUIRED, 'The WSDL on which you base the types')
            ->addOption('namespace', null, InputOption::VALUE_OPTIONAL, 'Resulting namespace')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $wsdl = $input->getOption('wsdl');
        if (!$wsdl) {
            throw new RunTimeException('You MUST specify a WSDL endpoint.');
        }

        $namespace = $input->getOption('namespace');
        $soapClient = new SoapClient($wsdl, []);
        $typeMap = TypeMap::fromSoapClient($namespace, $soapClient);
        
        $ruleSet = new RuleSet([
            new AssembleRule(new ClassMapAssembler())
        ]);

        $file = new FileGenerator();
        $generator = new ClassMapGenerator($ruleSet);
        $output->write($generator->generate($file, $typeMap));
    }
}
