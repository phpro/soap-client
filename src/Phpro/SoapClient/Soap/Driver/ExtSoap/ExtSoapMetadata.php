<?php

declare(strict_types=1);

namespace Phpro\SoapClient\Soap\Driver\ExtSoap;

use Phpro\SoapClient\Soap\Driver\ExtSoap\Metadata\MethodsParser;
use Phpro\SoapClient\Soap\Driver\ExtSoap\Metadata\TypesParser;
use Phpro\SoapClient\Soap\Driver\ExtSoap\Metadata\XsdTypesParser;
use Phpro\SoapClient\Soap\Engine\Metadata\Collection\MethodCollection;
use Phpro\SoapClient\Soap\Engine\Metadata\Collection\TypeCollection;
use Phpro\SoapClient\Soap\Engine\Metadata\Collection\XsdTypeCollection;
use Phpro\SoapClient\Soap\Engine\Metadata\MetadataInterface;

class ExtSoapMetadata implements MetadataInterface
{
    /**
     * @var AbusedClient
     */
    private $abusedClient;

    /**
     * @var XsdTypeCollection|null
     */
    private $xsdTypes;

    public function __construct(AbusedClient $abusedClient)
    {
        $this->abusedClient = $abusedClient;
    }

    public function getMethods(): MethodCollection
    {
        return (new MethodsParser($this->getXsdTypes()))->parse($this->abusedClient);
    }

    public function getTypes(): TypeCollection
    {
        return (new TypesParser($this->getXsdTypes()))->parse($this->abusedClient);
    }

    private function getXsdTypes(): XsdTypeCollection
    {
        if (null === $this->xsdTypes) {
            $this->xsdTypes = XsdTypesParser::default()->parse($this->abusedClient);
        }

        return $this->xsdTypes;
    }
}
