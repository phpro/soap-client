<?php

namespace Phpro\SoapClient\CodeGenerator\Model;

use Phpro\SoapClient\CodeGenerator\Util\Normalizer;

/**
 * Class Type
 *
 * @package Phpro\SoapClient\CodeGenerator\Model
 */
class Type
{

    /**
     * @var string
     */
    private $namespace;

    /**
     * @var string
     */
    private $xsdName;

    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $properties = [];


    /**
     * TypeModel constructor.
     *
     * @param string     $namespace
     * @param string     $name
     * @param Property[] $properties
     */
    public function __construct($namespace, $name, array $properties)
    {
        $this->namespace = Normalizer::normalizeNamespace($namespace);
        $this->xsdName = $name;
        $this->name = Normalizer::normalizeClassname($name);

        foreach ($properties as $property => $type) {
            $this->properties[] = new Property($property, $type);
        }
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getXsdName()
    {
        return $this->xsdName;
    }

    /**
     * @param $destination
     *
     * @return string
     */
    public function getPathname($destination)
    {
        return rtrim($destination, '/\\') . DIRECTORY_SEPARATOR . $this->getName() . '.php';
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return sprintf('%s\\%s', $this->getNamespace(), $this->getName());
    }

    /**
     * @return Property[]
     */
    public function getProperties()
    {
        return $this->properties;
    }
}
