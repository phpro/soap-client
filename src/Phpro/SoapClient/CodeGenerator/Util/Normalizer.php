<?php

namespace Phpro\SoapClient\CodeGenerator\Util;

/**
 * Class Normalizer
 *
 * @package Phpro\SoapClient\CodeGenerator\Util
 */
class Normalizer
{

    private static $normalizations = [
        'long' => 'int',
        'short' => 'int',
        'datetime' => '\\DateTime',
        'date' => '\\DateTime',
        'boolean' => 'bool',
        'decimal' => 'float',
        'double' => 'float',
        'string' => 'string',
        'self' => 'self',
        'callable' => 'callable',
        'iterable' => 'iterable',
        'array' => 'array',
    ];

    /**
     * @var array
     * @see https://secure.php.net/manual/en/reserved.keywords.php
     */
    private static $reservedKeywords = [
        '__halt_compiler',
        'abstract',
        'and',
        'array',
        'as',
        'break',
        'callable',
        'case',
        'catch',
        'class',
        'clone',
        'const',
        'continue',
        'declare',
        'default',
        'die',
        'do',
        'echo',
        'else',
        'elseif',
        'empty',
        'enddeclare',
        'endfor',
        'endforeach',
        'endif',
        'endswitch',
        'endwhile',
        'eval',
        'exit',
        'extends',
        'final',
        'finally',
        'for',
        'foreach',
        'function',
        'global',
        'goto',
        'if',
        'implements',
        'include',
        'include_once',
        'instanceof',
        'insteadof',
        'interface',
        'isset',
        'list',
        'namespace',
        'new',
        'or',
        'print',
        'private',
        'protected',
        'public',
        'require',
        'require_once',
        'return',
        'static',
        'switch',
        'throw',
        'trait',
        'try',
        'unset',
        'use',
        'var',
        'while',
        'xor',
        'yield',
    ];

    /**
     * @param string $name
     * @param bool   $ucfirst
     *
     * @return string
     */
    private static function normalizeReservedKeywords(string $name, $ucfirst = true): string
    {
        if (!\in_array(strtolower($name), self::$reservedKeywords, true)) {
            return $name;
        }
        $name .= 'Type';

        return $ucfirst ? ucfirst($name) : $name;
    }

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
     * Convert a word to camelCase or CamelCase (not changing first part!)
     *
     * @param string $word
     * @param string $regexp
     *
     * @return string
     */
    private static function camelCase(string $word, string $regexp):string
    {
        $parts = array_filter(preg_split($regexp, $word));
        $keepUnchanged = array_shift($parts);
        $parts = array_map('ucfirst', $parts);
        array_unshift($parts, $keepUnchanged);

        return implode('', $parts);
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public static function normalizeClassname(string $name): string
    {
        $name = self::normalizeReservedKeywords($name);

        return ucfirst(self::camelCase($name, '{[^a-z0-9]+}i'));
    }

    /**
     * @param string $property
     *
     * @return string
     */
    public static function normalizeProperty(string $property): string
    {
        $property = self::normalizeReservedKeywords($property, false);

        return self::camelCase($property, '{[^a-z0-9_]+}i');
    }

    /**
     * @param string $type
     *
     * @return string
     */
    public static function normalizeDataType(string $type): string
    {
        $searchType = strtolower($type);

        return array_key_exists($searchType, self::$normalizations) ? self::$normalizations[$searchType] : $type;
    }

    public static function isKnownType(string $type): bool
    {
        return \in_array($type, self::$normalizations, true);
    }

    /**
     * @param string $prefix
     * @param string $property
     *
     * @return string
     */
    public static function generatePropertyMethod(string $prefix, string $property): string
    {
        return strtolower($prefix).ucfirst(self::normalizeProperty($property));
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public static function getClassNameFromFQN(string $name): string
    {
        $arr = explode('\\', $name);

        return (string)array_pop($arr);
    }

    /**
     * @param string      $useName
     * @param string|null $useAlias
     *
     * @return string
     */
    public static function getCompleteUseStatement(string $useName, string $useAlias = null): string
    {
        $use = $useName;
        if (null !== $useAlias && $useAlias !== '') {
            $use .= ' as '.$useAlias;
        }

        return $use;
    }
}
