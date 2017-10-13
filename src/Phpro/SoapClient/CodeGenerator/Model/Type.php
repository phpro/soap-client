<?php

namespace Phpro\SoapClient\CodeGenerator\Model;

use Phpro\SoapClient\CodeGenerator\Util\Normalizer;
use SplFileInfo;

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
     * @param string     $xsdName
     * @param Property[] $properties
     */
    public function __construct($namespace, $xsdName, array $properties)
    {
        $this->namespace = Normalizer::normalizeNamespace($namespace);
        $this->xsdName = $xsdName;
        $this->name = Normalizer::normalizeClassname($xsdName);

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

    public function getFileInfo(string $destination) : SplFileInfo
    {
        $name = str_replace('_', DIRECTORY_SEPARATOR, $this->getName());
        $path = rtrim($destination, '/\\').DIRECTORY_SEPARATOR.$name.'.php';

        return new SplFileInfo($path);
    }

    /**
     * @param string $destination
     * @deprecated please use getFileInfo instead
     * @return string
     */
    public function getPathname($destination)
    {
        return $this->getFileInfo($destination)->getPathname();
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        $fqnName = sprintf('%s\\%s', $this->getNamespace(), $this->getName());

        return Normalizer::normalizeNamespace($fqnName);
    }

    /**
     * @return Property[]
     */
    public function getProperties()
    {
        return $this->properties;
    }
}
