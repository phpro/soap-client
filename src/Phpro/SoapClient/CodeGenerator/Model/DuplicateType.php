<?php

namespace Phpro\SoapClient\CodeGenerator\Model;

use Phpro\SoapClient\CodeGenerator\Util\Normalizer;

class DuplicateType
{
    /**
     * @var string
     */
    private $typeName;

    /**
     * @var string
     */
    private $xsdNamespace;

    /**
     * @var array
     */
    private $properties;

    public function __construct(string $typeName, string $xsdNamespace, array $properties)
    {
        $this->typeName = $typeName;
        $this->xsdNamespace = $xsdNamespace;
        $this->properties = $properties;
    }

    /**
     * Return true if type match criteria.
     *
     * @param Type $type
     * @return bool
     */
    public function matchType(Type $type)
    {
        if ($type->getName() !== $this->typeName) {
            return false;
        }

        if (count($type->getProperties()) !== count($this->properties)) {
            return false;
        }

        $propertyNames = [];
        /** @var Property $property */
        foreach ($type->getProperties() as $property) {
            $propertyNames[] = $property->getName();
        }

        // Type must contains all defined properties to match
        foreach ($this->properties as $property) {
            if (!in_array($property, $propertyNames)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return string Normalized namespace suffix
     */
    public function getNamespaceSuffix()
    {
        $namespaceParts = explode('/', $this->xsdNamespace);

        return Normalizer::normalizeClassname(end($namespaceParts));
    }

    /**
     * @return string
     */
    public function getTypeName(): string
    {
        return $this->typeName;
    }

    /**
     * @param string $typeName
     */
    public function setTypeName(string $typeName)
    {
        $this->typeName = $typeName;
    }

    /**
     * @return string
     */
    public function getXsdNamespace(): string
    {
        return $this->xsdNamespace;
    }

    /**
     * @param string $xsdNamespace
     */
    public function setXsdNamespace(string $xsdNamespace)
    {
        $this->xsdNamespace = $xsdNamespace;
    }

    /**
     * @return array
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * @param array $properties
     */
    public function setProperties(array $properties)
    {
        $this->properties = $properties;
    }
}
