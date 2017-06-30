<?php

namespace Phpro\SoapClient\CodeGenerator\Model;

use Zend\Code\Generator\ParameterGenerator;

/**
 * Class Parameter
 * @package Phpro\SoapClient\CodeGenerator\Model
 */
class Parameter extends ParameterGenerator
{

    /**
     * @var string
     */
    private $originalType;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $name;

    /**
     * Parameter constructor.
     * @param string $type
     */
    public function __construct($type)
    {
        $this->type = $type;
        $this->name = lcfirst($type);
        $this->originalType = $type;
        parent::__construct($this->name, 'Types\\'.$this->type);
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getOriginalType()
    {
        return $this->originalType;
    }
}
