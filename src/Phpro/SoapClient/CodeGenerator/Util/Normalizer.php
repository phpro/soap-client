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
    public static function normalizeNamespace($namespace)
    {
        return trim(str_replace('/', '\\', $namespace), '\\');
    }

    /**
     * @param $name
     *
     * @return string
     */
    public static function normalizeClassname($name)
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
    public static function normalizeDataType($type)
    {
        return strtr($type, [
            'long' => 'int',
            'short' => 'int',
            'dateTime' => '\\DateTime',
            'date' => '\\DateTime',
            'boolean' => 'bool',
        ]);
    }

    /**
     * @param $prefix
     * @param $property
     *
     * @return string
     */
    public static function generatePropertyMethod($prefix, $property)
    {
        return strtolower($prefix) . ucfirst(self::normalizeProperty($property));
    }

    /**
     * @param string $name
     * @return string
     */
    public static function lastPart($name)
    {
        $arr = explode('\\', $name);
        return array_pop($arr);
    }
}
