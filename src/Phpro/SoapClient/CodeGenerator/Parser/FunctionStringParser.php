<?php

namespace Phpro\SoapClient\CodeGenerator\Parser;

use Phpro\SoapClient\CodeGenerator\Model\Parameter;
use Phpro\SoapClient\CodeGenerator\Util\Normalizer;

/**
 * Class FunctionStringParser
 */
class FunctionStringParser
{
    /**
     * @var string
     */
    private $functionString;

    /**
     * @var string
     */
    private $parameterNamespace;

    /**
     * FunctionStringParser constructor.
     * @param        $functionString
     * @param string $parameterNamespace
     */
    public function __construct(string $functionString, string $parameterNamespace)
    {
        $this->functionString = $functionString;
        $this->parameterNamespace = $parameterNamespace;
    }

    /**
     * @return Parameter[]
     */
    public function parseParameters(): array
    {
        preg_match('/\((.*)\)/', $this->functionString, $properties);
        $parameters = [];
        $properties = preg_split('/,\s?/', $properties[1]);
        foreach ($properties as $property) {
            list($type, $name) = explode(' ', trim($property));
            $name = Normalizer::normalizeProperty($name);
            $type = Normalizer::normalizeClassname($type);
            $parameters[] = new Parameter($name, $this->parameterNamespace.'\\'.$type);
        }

        return $parameters;
    }

    /**
     * @return string
     */
    public function parseName(): string
    {
        preg_match('/^\w+ (\w+)/', $this->functionString, $matches);

        return $matches[1];
    }

    /**
     * @return mixed
     */
    public function parseReturnType()
    {
        preg_match('/^(\w+)/', $this->functionString, $matches);

        return $matches[1];
    }
}
