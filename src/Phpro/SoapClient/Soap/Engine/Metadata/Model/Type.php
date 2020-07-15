<?php

declare(strict_types=1);

namespace Phpro\SoapClient\Soap\Engine\Metadata\Model;

/**
 * @TODO V2.0 : Make properties instance of type PropertyCollection !
 */
class Type
{
    /**
     * @var XsdType
     */
    private $xsdType;

    /**
     * @var Property[]
     */
    private $properties = [];

    public function __construct(XsdType $xsdType, array $properties)
    {
        $this->xsdType = $xsdType;
        foreach ($properties as $property) {
            $this->addProperty($property);
        }
    }

    public function getName(): string
    {
        return $this->xsdType->getName();
    }

    public function getXsdType(): XsdType
    {
        return $this->xsdType;
    }

    /**
     * @return array|Property[]
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    public function addProperty(Property $property)
    {
        $this->properties[] = $property;
    }
}
