<?php

namespace Phpro\SoapClient\CodeGenerator\Model;

/**
 * Class Type
 *
 * @package Phpro\SoapClient\CodeGenerator\Model
 */
class Method
{
    /**
     * @var array|Method[]
     */
    private $parameters;

    /**
     * @var string
     */
    private $methodName;

    /**
     * @var string
     */
    private $returnType;

    /**
     * TypeModel constructor.
     *
     * @param $functionString
     * @internal param string $xsdName
     * @internal param Property[] $properties
     */
    public function __construct($functionString)
    {
        $this->parameters = $this->parseParameters($functionString);
        $this->methodName = $this->parseName($functionString);
        $this->returnType = $this->parseReturnType($functionString);
    }

    /**
     * @param $str
     * @return array
     */
    private function parseParameters($str)
    {
        preg_match(
            '/\((.*)\)/',
            $str,
            $properties
        );

        $parameters = [];
        $properties = explode(',', $properties[1]);
        foreach ($properties as $property) {
            list($type) = explode(' ', $property);
            $parameters[] = new Parameter($type);
        }

        return $parameters;
    }

    /**
     * @param $functionString
     * @return string
     */
    private function parseName($functionString)
    {
        preg_match('/^\w+ (\w+)/', $functionString, $matches);

        return $matches[1];
    }

    /**
     * @param $functionString
     * @return mixed
     */
    private function parseReturnType($functionString)
    {
        preg_match('/^(\w+)/', $functionString, $matches);

        return $matches[1];
    }

    /**
     * @return array|Parameter[]
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @return string
     */
    public function getMethodName()
    {
        return lcfirst($this->methodName);
    }

    /**
     * @return string
     */
    public function getReturnType()
    {
        return $this->returnType;
    }
}
