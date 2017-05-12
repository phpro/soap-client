<?php

namespace Phpro\SoapClient\CodeGenerator\Model;

use Phpro\SoapClient\CodeGenerator\Util\Normalizer;

/**
 * Class Property
 *
 * @package Phpro\SoapClient\CodeGenerator\Model
 */
class Property
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $originalType;

    /**
     * Property constructor.
     *
     * @param string $name
     * @param string $type
     */
    public function __construct($name, $type)
    {
        $this->name = Normalizer::normalizeProperty($name);
        $this->type = Normalizer::normalizeDataType($type);
        $this->originalType = $type;
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
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getOriginalType()
    {
        return $this->originalType;
    }

    /**
     * @return string
     */
    public function getterName()
    {
        return Normalizer::generatePropertyMethod('get', $this->getName());
    }

    /**
     * @return string
     */
    public function setterName()
    {
        return Normalizer::generatePropertyMethod('set', $this->getName());
    }
}
