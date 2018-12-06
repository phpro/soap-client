<?php

declare(strict_types=1);

namespace Phpro\SoapClient\Soap\Driver\ExtSoap;

use Phpro\SoapClient\Soap\Driver\ExtSoap\Metadata\MethodsParser;
use Phpro\SoapClient\Soap\Driver\ExtSoap\Metadata\TypesParser;
use Phpro\SoapClient\Soap\Engine\Metadata\Collection\MethodCollection;
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

    public function getMethods(): MethodCollection
    {
        return (new MethodsParser())->parse($this->abusedClient);
    }

    public function getTypes(): TypeCollection
    {
        return (new TypesParser())->parse($this->abusedClient);
    }
}
