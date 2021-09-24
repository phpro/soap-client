<?php

declare(strict_types=1);

namespace Phpro\SoapClient\Soap\Metadata\Detector;

use Soap\Engine\Metadata\Collection\MethodCollection;
use Soap\Engine\Metadata\Model\Method;

final class ResponseTypesDetector
{
    public function __invoke(MethodCollection $methods): array
    {
        return array_unique(array_map(
            static function (Method $method): string {
                return $method->getReturnType()->getName();
            },
            iterator_to_array($methods)
        ));
    }
}
