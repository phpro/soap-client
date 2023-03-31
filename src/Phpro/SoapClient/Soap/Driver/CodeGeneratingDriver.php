<?php
declare(strict_types=1);

namespace Phpro\SoapClient\Soap\Driver;

use Soap\Engine\Driver;
use Soap\Engine\HttpBinding\SoapRequest;
use Soap\Engine\HttpBinding\SoapResponse;
use Soap\Engine\Metadata\Metadata;

/**
 * This driver can be used for code-generation only.
 * Once we have raw PHP based encoding/decoding of SOAP inside php-soap, this class will go away.
 *
 * @internal
 */
class CodeGeneratingDriver implements Driver
{
    private Metadata $metadata;

    public function __construct(Metadata $metadata)
    {
        $this->metadata = $metadata;
    }

    /**
     * @return never
     */
    public function decode(string $method, SoapResponse $response)
    {
        throw new \RuntimeException('The code-generator driver should not be used to request SOAP');
    }

    /**
     * @return never
     */
    public function encode(string $method, array $arguments): SoapRequest
    {
        throw new \RuntimeException('The code-generator driver should not be used to request SOAP');
    }

    public function getMetadata(): Metadata
    {
        return $this->metadata;
    }
}
