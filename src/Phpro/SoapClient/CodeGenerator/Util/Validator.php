<?php

namespace Phpro\SoapClient\CodeGenerator\Util;

use Phpro\SoapClient\Console\Command\WizardCommand;
use Laminas\Code\Generator\ClassGenerator;

class Validator
{
    public static function laminasCodeIsInstalled(): bool
    {
        return class_exists(ClassGenerator::class);
    }

    public static function commandRequiresLaminasCode(string $name): bool
    {
        if ($name === WizardCommand::COMMAND_NAME) {
            return true;
        }

        return strpos($name, 'generate') === 0;
    }
}
