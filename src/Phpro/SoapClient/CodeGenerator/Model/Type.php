<?php

namespace Phpro\SoapClient\CodeGenerator\Model;

use Phpro\SoapClient\CodeGenerator\Util\Normalizer;
use Phpro\SoapClient\Soap\Engine\Metadata\Model\Property as MetadataProperty;
use Phpro\SoapClient\Soap\Engine\Metadata\Model\Type as MetadataType;
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
        $this->properties = $properties;
    }

    public static function fromMetadata(string $namespace, MetadataType $type): self
    {
        return new self(
            $namespace,
            $type->getName(),
            array_map(
                function (MetadataProperty $property) use ($namespace) {
                    return Property::fromMetaData(
                        $namespace,
                        $property
                    );
                },
                $type->getProperties()
            )
        );
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
    public function getFileInfo(string $destination): SplFileInfo
    {
        $name = Normalizer::normalizeClassname($this->getName());
        $path = rtrim($destination, '/\\').'/'.$name.'.php';

        return new SplFileInfo($path);
    }

    /**
     * @param string $destination
     *
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
