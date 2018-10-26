<?php

declare(strict_types=1);

namespace Phpro\SoapClient\Soap\Driver\ExtSoap;

use Phpro\SoapClient\CodeGenerator\Model\ClientMethodMap;
use Phpro\SoapClient\Soap\Driver\ExtSoap\Metadata\TypesParser;
use Phpro\SoapClient\Soap\Engine\Metadata\Collection\TypeCollection;
use Phpro\SoapClient\Soap\Engine\Metadata\MetadataInterface;

class ExtSoapMetadata implements MetadataInterface
{
    /**
     * @var AbusedClient
     */
    private $abusedClient;

    public function __construct(AbusedClient $abusedClient)
    {
        $this->abusedClient = $abusedClient;
    }

    public function getMethods(): ClientMethodMap
    {
        return ClientMethodMap::fromSoapClient($this->abusedClient);
    }

    public function getTypes(): TypeCollection
    {
        return (new TypesParser())->parse($this->abusedClient);
    }
}
