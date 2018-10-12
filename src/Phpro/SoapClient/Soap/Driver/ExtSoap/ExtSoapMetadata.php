<?php

declare(strict_types=1);

namespace Phpro\SoapClient\Soap\Driver\ExtSoap;

use Phpro\SoapClient\CodeGenerator\Model\ClientMethodMap;
use Phpro\SoapClient\Soap\Engine\Metadata\MetadataInterface;

class ExtSoapMetadata implements MetadataInterface
{
    /**
     * @var AbusedClient
     */
    private $abusedClient;

    /**
     * SOAP types derived from WSDLss
     *
     * @var array
     */
    private $types = [];

    public function __construct(AbusedClient $abusedClient)
    {
        $this->abusedClient = $abusedClient;
    }

    public function getMethods(): ClientMethodMap
    {
        return ClientMethodMap::fromSoapClient($this->abusedClient);
    }

    /**
     * Retrieve SOAP types from the WSDL and parse them
     *
     * @return array Array of types and their properties
     */
    public function getTypes(): array
    {
        if ($this->types) {
            return $this->types;
        }

        $soapTypes = $this->abusedClient->__getTypes();
        foreach ($soapTypes as $soapType) {
            $properties = [];
            $lines = explode("\n", $soapType);
            if (!preg_match('/struct (.*) {/', $lines[0], $matches)) {
                continue;
            }
            $typeName = $matches[1];

            foreach (array_slice($lines, 1) as $line) {
                if ($line === '}') {
                    continue;
                }
                preg_match('/\s* (.*) (.*);/', $line, $matches);
                $properties[$matches[2]] = $matches[1];
            }

            $this->types[$typeName] = $properties;
        }

        return $this->types;
    }
}
