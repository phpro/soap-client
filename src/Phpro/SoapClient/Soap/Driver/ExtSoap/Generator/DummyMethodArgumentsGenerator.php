<?php

declare(strict_types=1);

namespace Phpro\SoapClient\Soap\Driver\ExtSoap\Generator;

use Phpro\SoapClient\Soap\Engine\Metadata\MetadataInterface;

/**
 * For decoding the soap response, we require that the __soapCall takes the same amount of arguments.
 * If a, this causes segfaults when using a type map.
 */
class DummyMethodArgumentsGenerator
{
    /**
     * @var MetadataInterface
     */
    private $metadata;

    public function __construct(MetadataInterface $metadata)
    {
        $this->metadata = $metadata;
    }

    public function generateForSoapCall(string $method): array
    {
        $methods = $this->metadata->getMethods();
        $method = $methods->fetchOneByName($method);

        return array_fill(0, \count($method->getParameters()), null);
    }
}
