<?php

declare(strict_types=1);

namespace Phpro\SoapClient\Soap\Engine\Metadata\Manipulators;

use Phpro\SoapClient\Soap\Engine\Metadata\Collection\MethodCollection;

final class MethodsManipulatorChain implements MethodsManipulatorInterface
{
    /**
     * @var MethodsManipulatorInterface[]
     */
    private $manipulators;

    public function __construct(MethodsManipulatorInterface ...$manipulators)
    {
        $this->manipulators = $manipulators;
    }

    public function __invoke(MethodCollection $allMethods): MethodCollection
    {
        return array_reduce(
            $this->manipulators,
            static function (MethodCollection $methods, MethodsManipulatorInterface $manipulator): MethodCollection {
                return $manipulator($methods);
            },
            $allMethods
        );
    }
}
