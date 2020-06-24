<?php

declare(strict_types=1);

namespace Phpro\SoapClient\Soap\Engine\Metadata;

use Phpro\SoapClient\Soap\Engine\Metadata\Manipulators\MethodsManipulatorInterface;
use Phpro\SoapClient\Soap\Engine\Metadata\Manipulators\MethodsManipulatorChain;
use Phpro\SoapClient\Soap\Engine\Metadata\Manipulators\TypesManipulatorInterface;
use Phpro\SoapClient\Soap\Engine\Metadata\Manipulators\TypesManipulatorChain;

final class MetadataOptions
{
    /**
     * @var MethodsManipulatorInterface
     */
    private $methodsManipulator;

    /**
     * @var TypesManipulatorInterface
     */
    private $typesManipulator;

    public function __construct(
        MethodsManipulatorInterface $methodsManipulator,
        TypesManipulatorInterface $typesManipulator
    ) {
        $this->methodsManipulator = $methodsManipulator;
        $this->typesManipulator = $typesManipulator;
    }

    public static function empty(): self
    {
        return new self(new MethodsManipulatorChain(), new TypesManipulatorChain());
    }

    public function withMethodsManipulator(MethodsManipulatorInterface $methodsManipulator): self
    {
        $new = clone $this;
        $new->methodsManipulator = $methodsManipulator;

        return $new;
    }

    public function withTypesManipulator(TypesManipulatorInterface $typesManipulator): self
    {
        $new = clone $this;
        $new->typesManipulator = $typesManipulator;

        return $new;
    }

    public function getMethodsManipulator(): MethodsManipulatorInterface
    {
        return $this->methodsManipulator;
    }

    public function getTypesManipulator(): TypesManipulatorInterface
    {
        return $this->typesManipulator;
    }
}
