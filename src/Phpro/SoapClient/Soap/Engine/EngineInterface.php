<?php

declare(strict_types=1);

namespace Phpro\SoapClient\Soap\Engine;

interface EngineInterface extends EncoderInterface, DecoderInterface
{
    public function getMetadata(): MetadataInterface;
    public function request(string $method, array $arguments);
}
