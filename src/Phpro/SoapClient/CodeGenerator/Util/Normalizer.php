<?php

namespace Phpro\SoapClient\CodeGenerator\Util;

/**
 * Class Normalizer
 *
 * @package Phpro\SoapClient\CodeGenerator\Util
 */
class Normalizer
{

    /**
     * @param string $namespace
     *
     * @return string
     */
    public static function normalizeNamespace(string $namespace): string
    {
        return trim(str_replace('/', '\\', $namespace), '\\');
    }

    /**
     * @param $name
     *
     * @return string
     */
    public static function normalizeClassname($name): string
    {
        return ucfirst(preg_replace('{[^a-z0-9_]}i', '', $name));
    }

    /**
     * @param $property
     *
     * @return mixed
     */
    public static function normalizeProperty($property)
    {
        return preg_replace('{[^a-z0-9_]}i', '', $property);
    }

    /**
     * @param string $type
     *
     * @return string
     */
    public static function normalizeDataType(string $type): string
    {
        return strtr($type, [
            'long' => 'int',
            'short' => 'int',
            'dateTime' => '\\DateTime',
            'date' => '\\DateTime',
            'boolean' => 'bool',
            'decimal' => 'float',
            'double' => 'float',
        ]);
    }

    /**
     * @param string $prefix
     * @param string $property
     *
     * @return string
     */
    public static function generatePropertyMethod(string $prefix, string $property): string
    {
        return strtolower($prefix) . ucfirst(self::normalizeProperty($property));
    }

    /**
     * @param string $name
     * @return string
     */
    public static function getClassNameFromFQN(string $name): string
    {
        $arr = explode('\\', $name);
        return (string)array_pop($arr);
    }

    /**
     * @param string $useName
     * @param string $useAlias
     * @return string
     */
    public static function getCompleteUseStatement(string $useName, string $useAlias): string
    {
        $use = $useName;
        if (!empty($useAlias)) {
            $use .= ' as ' . $useAlias;
        }
        return $use;
    }
}
