<?php

declare(strict_types=1);

namespace Phpro\SoapClient\Soap\Engine\Metadata\Model;

class XsdType
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $baseType = '';

    /**
     * @var string[]
     */
    private $memberTypes = [];

    /**
     * @var string
     */
    private $xmlNamespace = '';

    /**
     * @var string
     */
    private $xmlNamespaceName = '';

    private function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function create(string $name): self
    {
        return new self($name);
    }

    public static function guess(string $name): self
    {
        return self::create($name)
           ->withBaseType(self::convertBaseType($name, ''));
    }

    public static function fetchAllKnownBaseTypeMappings(): array
    {
        return [
            'anyuri' => 'string',
            'base64binary' => 'string',
            'byte' => 'integer',
            'decimal' => 'float',
            'double' => 'float',
            'duration' => 'string',
            'entities' => 'string',
            'entity' => 'string',
            'gday' => 'string',
            'gmonth' => 'string',
            'gmonthday' => 'string',
            'gyear' => 'string',
            'gyearmonth' => 'string',
            'hexbinary' => 'string',
            'id' => 'string',
            'idref' => 'string',
            'idrefs' => 'string',
            'int' => 'integer',
            'language' => 'string',
            'long' => 'integer',
            'map' => 'array',
            'name' => 'string',
            'ncname' => 'string',
            'ncnames' => 'string',
            'negativeinteger' => 'integer',
            'nmtoken' => 'string',
            'nmtokens' => 'string',
            'nonnegativeinteger' => 'integer',
            'nonpositiveinteger' => 'integer',
            'normalizedstring' => 'string',
            'notation' => 'string',
            'positiveinteger' => 'integer',
            'qname' => 'string',
            'short' => 'integer',
            'struct' => 'object',
            'time' => 'string',
            'timeinstant' => 'string',
            'token' => 'string',
            'unknown' => 'anyType',
            'unsignedbyte' => 'integer',
            'unsignedint' => 'integer',
            'unsignedlong' => 'integer',
            'unsignedshort' => 'integer',
        ];
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getBaseType(): string
    {
        return $this->baseType;
    }

    public function getBaseTypeOrFallbackToName(): string
    {
        return $this->baseType ?: $this->name;
    }

    public function getMemberTypes(): array
    {
        return $this->memberTypes;
    }

    public function getXmlNamespace(): string
    {
        return $this->xmlNamespace;
    }

    public function getXmlNamespaceName(): string
    {
        return $this->xmlNamespaceName;
    }

    public function withXmlNamespace(string $xmlNamespace): self
    {
        $new = clone $this;
        $new->xmlNamespace = $xmlNamespace;

        return $new;
    }

    public function withXmlNamespaceName(string $xmlNamespaceName): self
    {
        $new = clone $this;
        $new->xmlNamespaceName = $xmlNamespaceName;

        return $new;
    }

    public function withBaseType(string $baseType): self
    {
        $new = clone $this;
        $new->baseType = self::convertBaseType($baseType, $baseType);

        if ($new->baseType !== $baseType) {
            $new->memberTypes[] = $baseType;
        }

        return $new;
    }

    public function withMemberTypes(array $memberTypes): self
    {
        $new = clone $this;
        $new->memberTypes = array_values(array_filter($memberTypes));

        return $new;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    private static function convertBaseType(string $baseType, string $fallback): string
    {
        return self::fetchAllKnownBaseTypeMappings()[strtolower($baseType)] ?? $fallback;
    }
}
