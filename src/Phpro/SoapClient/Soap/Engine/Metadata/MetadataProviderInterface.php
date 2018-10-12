<?php

declare( strict_types=1 );

namespace Phpro\SoapClient\Soap\Engine\Metadata;

interface MetadataProviderInterface
{
    public function getMetadata(): MetadataInterface;
}
