<?php

declare(strict_types=1);

namespace Phpro\SoapClient\Soap\Engine;

use Phpro\SoapClient\CodeGenerator\Model\ClientMethodMap;

interface MetadataInterface
{
    public function getTypes(): array;
    public function getMethods(): ClientMethodMap;
}
