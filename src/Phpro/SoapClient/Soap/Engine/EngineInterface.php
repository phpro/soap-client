<?php

declare(strict_types=1);

namespace Phpro\SoapClient\Soap\Engine;

use Phpro\SoapClient\Soap\Engine\Metadata\MetadataProviderInterface;
use Phpro\SoapClient\Soap\Handler\LastRequestInfoCollectorInterface;

interface EngineInterface extends MetadataProviderInterface, LastRequestInfoCollectorInterface
{
    public function request(string $method, array $arguments);
}
