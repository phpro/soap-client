<?php

namespace Phpro\SoapClient\Soap\TypeConverter;

use Phpro\SoapClient\Exception\InvalidArgumentException;

/**
 * Class TypeConverterCollection
 *
 *  A collection of type converters
 *
 * @package Phpro\SoapClient\Soap\TypeConverter
 */
class TypeConverterCollection
{
    /**
     * @var array|TypeConverterInterface[]
     */
    protected $converters = [];

    /**
     * Construct type converter collection
     *
     * @param array $converters (optional) Array of type converters
     */
    public function __construct(array $converters = [])
    {
        foreach ($converters as $converter) {
            $this->add($converter);
        }
    }

    /**
     * @param TypeConverterInterface $converter
     *
     * @return string
     */
    private function serialize(TypeConverterInterface $converter)
    {
        return $converter->getTypeNamespace() . ':' . $converter->getTypeName();
    }

    /**
     * Add a type converter to the collection
     *
     * @param TypeConverterInterface $converter Type converter
     *
     * @return TypeConverterCollection
     * @throws InvalidArgumentException
     */
    public function add(TypeConverterInterface $converter)
    {
        if ($this->has($converter)) {
            throw new InvalidArgumentException(
                'Converter for this type already exists'
            );
        }

        return $this->set($converter);
    }

    /**
     * Set (overwrite) a type converter in the collection 
     *
     * @param TypeConverterInterface $converter Type converter
     *
     * @return TypeConverterCollection
     */
    public function set(TypeConverterInterface $converter)
    {
        $serialized = $this->serialize($converter);
        $this->converters[$serialized] = $converter;

        return $this;
    }

    /**
     * Returns true if the collection contains a type converter for a certain
     * namespace and name
     *
     * @param TypeConverterInterface $converter
     *
     * @return bool
     */
    public function has(TypeConverterInterface $converter)
    {
        $serialized = $this->serialize($converter);
        if (isset($this->converters[$serialized])) {
            return true;
        }

        return false;
    }

    /**
     * Get this collection as a typemap that can be used in PHP's \SoapClient
     * 
     * @return array
     */
    public function toSoapTypeMap()
    {
        $typemap = [];

        foreach ($this->converters as $converter) {
            $typemap[] = [
                'type_name' => $converter->getTypeName(),
                'type_ns'   => $converter->getTypeNamespace(),
                'from_xml'  => function($input) use ($converter) {
                    return $converter->convertXmlToPhp($input);
                },
                'to_xml'    => function($input) use ($converter) {
                    return $converter->convertPhpToXml($input);
                },
            ];
        }

        return $typemap;
    }
}