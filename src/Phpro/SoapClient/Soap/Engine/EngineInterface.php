<?php

declare(strict_types=1);

namespace Phpro\SoapClient\Soap\Engine;

use Phpro\SoapClient\Soap\Engine\Metadata\MetadataProviderInterface;

interface EngineInterface extends MetadataProviderInterface
{
    public function request(string $method, array $arguments);
}
