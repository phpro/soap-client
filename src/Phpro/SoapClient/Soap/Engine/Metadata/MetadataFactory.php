<?php

declare(strict_types=1);

namespace Phpro\SoapClient\Soap\Engine\Metadata;

class MetadataFactory
{
    public static function lazy(MetadataInterface $metadata): MetadataInterface
    {
        return new LazyInMemoryMetadata($metadata);
    }

    public static function manipulated(MetadataInterface $metadata, MetadataOptions $options): MetadataInterface
    {
        return self::lazy(
            new ManipulatedMetadata(
                $metadata,
                $options->getMethodsManipulator(),
                $options->getTypesManipulator()
            )
        );
    }
}
