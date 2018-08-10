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
    public function __construct(string $namespace, string $xsdName, array $properties)
    {
        $this->namespace = Normalizer::normalizeNamespace($namespace);
        $this->xsdName = $xsdName;
        $this->name = Normalizer::normalizeClassname($xsdName);

        foreach ($properties as $property => $type) {
            $this->properties[] = new Property($property, $type, $this->namespace);
        }
    }

    /**
     * @return string
     */
    public function getNamespace(): string
    {
        return $this->namespace;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getXsdName(): string
    {
        return $this->xsdName;
    }


    /**
     * @param string $destination
     *
     * @return SplFileInfo
     */
    public function getFileInfo(string $destination) : SplFileInfo
    {
        $path = rtrim($destination, '/\\').DIRECTORY_SEPARATOR.$this->getName().'.php';

        return new SplFileInfo($path);
    }

    /**
     * @param string $destination
     * @deprecated please use getFileInfo instead
     * @return string
     */
    public function getPathname(string $destination): string
    {
        return $this->getFileInfo($destination)->getPathname();
    }

    /**
     * @return string
     */
    public function getFullName(): string
    {
        $fqnName = sprintf('%s\\%s', $this->getNamespace(), $this->getName());

        return Normalizer::normalizeNamespace($fqnName);
    }

    /**
     * @return Property[]
     */
    public function getProperties(): array
    {
        return $this->properties;
    }
}
