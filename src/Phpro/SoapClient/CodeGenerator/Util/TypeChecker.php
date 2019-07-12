<?php

declare(strict_types=1);

namespace Phpro\SoapClient\CodeGenerator\Util;

/**
 * Class TypeChecker
 *
 * @package Phpro\SoapClient\CodeGenerator\Util
 */
class TypeChecker
{
    /**
     * @var string[]
     *
     * @link http://php.net/manual/en/functions.arguments.php#functions.arguments.type-declaration
     */
    private static $internalPhpTypes = ['void', 'int', 'float', 'string', 'bool', 'array', 'callable', 'iterable'];

    /**
     * @param string $type
     *
     * @return bool
     */
    public static function isKnownType(string $type): bool
    {
        return self::isInternalPhpType($type) || self::isClassType($type);
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    private static function isInternalPhpType(string $type): bool
    {
        return in_array(strtolower($type), self::$internalPhpTypes, true);
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    private static function isClassType(string $type): bool
    {
        //todo add check for available classes

        return false;
    }
}
