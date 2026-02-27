<?php

declare(strict_types=1);

namespace Phpro\SoapClient\CodeGenerator\Provider;

use Phpro\SoapClient\CodeGenerator\Model\Property;
use Psl\Result\ResultInterface;
use Soap\WsdlReader\Metadata\Predicate\IsConsideredNullableType;
use function Psl\Result\wrap;

final class ScalarDefaultProvider
{
    /**
     * @return ResultInterface<mixed>
     */
    public function __invoke(Property $property): ResultInterface
    {
        return wrap(function () use ($property): mixed {
            if ((new IsConsideredNullableType())($property->getMeta())) {
                return null;
            }

            return match ($property->getPhpType()) {
                'mixed' => null,
                'string' => '',
                'int' => 0,
                'bool' => false,
                'float' => 0.0,
                'array' => [],
                default => throw new \RuntimeException('Type with unknown default: ' . $property->getPhpType()),
            };
        });
    }
}
