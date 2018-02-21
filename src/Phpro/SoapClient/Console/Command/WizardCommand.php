<?php

declare(strict_types=1);

namespace Phpro\SoapClient\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class WizardCommand extends Command
{
    const COMMAND_NAME = 'wizard';

    /**
     * Configure the command.
     */
    protected function configure()
    {
        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription('Runs all generators to get you starting.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $config = $io->ask('Where would you like to store your config file?', 'config/soap-client.php');

        $pipeline = [
            'generate:config' => [],
            'generate:types' => [],
            'generate:classmap' => [],
            'generate:client' => [],
            'generate:clientfactory' => [],
        ];

        $application = $this->getApplication();
        foreach ($pipeline as $commandName => $arguments) {
            $command = $application->find($commandName);
            $commandInput = new ArrayInput(
                ['command' => $commandName, '--config' => $config] + $arguments
            );

            if (($exitCode = $command->run($commandInput, $output)) > 0) {
                return $exitCode;
            }
        }

        return 0;
    }
}
