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

    /**
     * @deprecated use laminasCodeIsInstalled() instead
     * @see self::laminasCodeIsInstalled()
     */
    public static function zendCodeIsInstalled(): bool
    {
        return self::laminasCodeIsInstalled();
    }

    public static function commandRequiresLaminasCode(string $name): bool
    {
        if ($name === WizardCommand::COMMAND_NAME) {
            return true;
        }

        return strpos($name, 'generate') === 0;
    }

    /**
     * @deprecated use commandRequiresLaminasCode() instead
     * @see self::commandRequiresLaminasCode()
     */
    public static function commandRequiresZendCode(string $name): bool
    {
        return self::commandRequiresLaminasCode($name);
    }
}
