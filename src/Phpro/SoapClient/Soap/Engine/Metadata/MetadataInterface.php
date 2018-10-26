<?php

declare(strict_types=1);

namespace Phpro\SoapClient\Soap\Engine\Metadata;

use Phpro\SoapClient\CodeGenerator\Model\ClientMethodMap;
use Phpro\SoapClient\Soap\Engine\Metadata\Collection\TypeCollection;

interface MetadataInterface
{
    public function getTypes(): TypeCollection;
    public function getMethods(): ClientMethodMap;
}
