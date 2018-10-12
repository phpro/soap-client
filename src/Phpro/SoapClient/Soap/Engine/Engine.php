<?php

declare(strict_types=1);

namespace Phpro\SoapClient\Soap\Engine;

use Phpro\SoapClient\Soap\Handler\HandlerInterface;
use Phpro\SoapClient\Soap\HttpBinding\SoapRequest;
use Phpro\SoapClient\Soap\HttpBinding\SoapResponse;

class Engine implements EngineInterface
{
    /**
     * @var MetadataInterface
     */
    private $metadata;

    /**
     * @var EncoderInterface
     */
    private $encoder;

    /**
     * @var DecoderInterface
     */
    private $decoder;

    /**
     * @var HandlerInterface
     */
    private $handler;

    public function __construct(
        MetadataInterface $metadata,
        EncoderInterface $encoder,
        DecoderInterface $decoder,
        HandlerInterface $handler
    ) {
        $this->encoder = $encoder;
        $this->decoder = $decoder;
        $this->handler = $handler;
        $this->metadata = $metadata;
    }

    public function getMetadata(): MetadataInterface
    {
        return $this->metadata;
    }

    public function request(string $method, array $arguments)
    {
        $request = $this->encode($method, $arguments);
        $response = $this->handler->request($request);

        return $this->decode($method, $response);
    }

    public function encode(string $method, array $arguments): SoapRequest
    {
        return $this->encoder->encode($method, $arguments);
    }

    public function decode(string $method, SoapResponse $response)
    {
        return $this->decoder->decode($method, $response);
    }
}
