<?php

declare(strict_types=1);

namespace Phpro\SoapClient\Soap\Metadata\Detector;

use Soap\Engine\Metadata\Collection\MethodCollection;
use Soap\Engine\Metadata\Model\Method;
use Soap\Engine\Metadata\Model\Parameter;

final class RequestTypesDetector
{
    public function __invoke(MethodCollection $methods): array
    {
        return array_unique(array_reduce(
            iterator_to_array($methods),
            static function (array $list, Method $method): array {
                if (count($method->getParameters()) === 1) {
                    /** @var Parameter $param */
                    $param = current(iterator_to_array($method->getParameters()));
                    $list[] = $param->getType()->getName();
                }

                return $list;
            },
            []
        ));
    }
}
