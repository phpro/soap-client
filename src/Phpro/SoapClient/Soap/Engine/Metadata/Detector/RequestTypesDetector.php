<?php

declare(strict_types=1);

namespace Phpro\SoapClient\Soap\Engine\Metadata\Detector;

use Phpro\SoapClient\Soap\Engine\Metadata\Collection\MethodCollection;
use Phpro\SoapClient\Soap\Engine\Metadata\Model\Method;
use Phpro\SoapClient\Soap\Engine\Metadata\Model\Parameter;

final class RequestTypesDetector
{
    public function __invoke(MethodCollection $methods): array
    {
        return array_unique(array_reduce(
            iterator_to_array($methods),
            static function (array $list, Method $method): array {
                if (count($method->getParameters()) === 1) {
                    /** @var Parameter $param */
                    $param = current($method->getParameters());
                    $list[] = $param->getType()->getName();
                }

                return $list;
            },
            []
        ));
    }
}
