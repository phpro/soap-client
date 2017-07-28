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
     * Parameter constructor.
     * @param string $name
     * @param string $type
     */
    public function __construct($name, $type)
    {
        parent::__construct($name, $type);
        $this->originalType = $type;
    }

    /**
     * @return string
     */
    public function getOriginalType()
    {
        return $this->originalType;
    }
}
