<?php

declare(strict_types=1);

namespace Phpro\SoapClient\Soap\Driver\ExtSoap\Metadata;

use Phpro\SoapClient\Soap\Driver\ExtSoap\AbusedClient;
use Phpro\SoapClient\Soap\Engine\Metadata\Collection\MethodCollection;
use Phpro\SoapClient\Soap\Engine\Metadata\Collection\XsdTypeCollection;
use Phpro\SoapClient\Soap\Engine\Metadata\Model\Method;
use Phpro\SoapClient\Soap\Engine\Metadata\Model\Parameter;
use Phpro\SoapClient\Soap\Engine\Metadata\Model\XsdType;

class MethodsParser
{
    /**
     * @var XsdTypeCollection
     */
    private $xsdTypes;

    public function __construct(XsdTypeCollection $xsdTypes)
    {
        $this->xsdTypes = $xsdTypes;
    }

    public function parse(AbusedClient $abusedClient): MethodCollection
    {
        return new MethodCollection(...array_map(
            function (string $methodString) {
                return $this->parseMethodFromString($methodString);
            },
            $abusedClient->__getFunctions()
        ));
    }

    private function parseMethodFromString(string $methodString): Method
    {
        $methodString = $this->transformListResponseToArray($methodString);
        return new Method(
            $this->parseName($methodString),
            $this->parseParameters($methodString),
            $this->parseReturnType($methodString)
        );
    }

    private function transformListResponseToArray(string $methodString): string
    {
        return preg_replace('/^list\(([^\)]*)\)(.*)/i', 'array$2', $methodString);
    }

    /**
     * @return Parameter[]
     */
    private function parseParameters(string $methodString): array
    {
        preg_match('/\((.*)\)/', $methodString, $properties);
        if (!$properties[1]) {
            return [];
        }

        $parameters = preg_split('/,\s?/', $properties[1]);

        return array_map(
            function (string $parameter): Parameter {
                list($type, $name) = explode(' ', trim($parameter));

                return new Parameter(
                    ltrim($name, '$'),
                    $this->xsdTypes->fetchByNameWithFallback($type)
                );
            },
            $parameters
        );
    }

    private function parseName(string $methodString): string
    {
        preg_match('/^\w+ (?P<name>\w+)/', $methodString, $matches);

        return (string) $matches['name'];
    }

    private function parseReturnType(string $methodString): XsdType
    {
        preg_match('/^(?P<returnType>\w+)/', $methodString, $matches);

        return $this->xsdTypes->fetchByNameWithFallback((string) $matches['returnType']);
    }
}
