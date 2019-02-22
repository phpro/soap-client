<?php

declare(strict_types=1);

namespace Phpro\SoapClient\Soap\Driver\ExtSoap\Metadata\Visitor;

use Phpro\SoapClient\Soap\Engine\Metadata\Model\XsdType;

interface XsdTypeVisitorInterface
{
    public function __invoke(string $soapType): ?XsdType;
}
