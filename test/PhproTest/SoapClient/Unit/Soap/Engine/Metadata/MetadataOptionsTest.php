<?php

declare(strict_types=1);

namespace PhproTest\SoapClient\Unit\Soap\Engine\Metadata;

use Phpro\SoapClient\Soap\Engine\Metadata\Manipulators\MethodsManipulatorChain;
use Phpro\SoapClient\Soap\Engine\Metadata\Manipulators\MethodsManipulatorInterface;
use Phpro\SoapClient\Soap\Engine\Metadata\Manipulators\TypesManipulatorChain;
use Phpro\SoapClient\Soap\Engine\Metadata\Manipulators\TypesManipulatorInterface;
use Phpro\SoapClient\Soap\Engine\Metadata\MetadataOptions;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class MetadataOptionsTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var MetadataOptions
     */
    private $metaOptions;

    protected function setUp(): void
    {
        $this->metaOptions = MetadataOptions::empty();
    }


    /** @test */
    public function it_contains_empty_chains_on_startup(): void
    {

        self::assertEquals(new TypesManipulatorChain(), $this->metaOptions->getTypesManipulator());
        self::assertEquals(new MethodsManipulatorChain(), $this->metaOptions->getMethodsManipulator());
    }

    /** @test */
    public function it_is_possible_to_change_types_manipulator(): void
    {
        $manipulator = $this->prophesize(TypesManipulatorInterface::class);
        $new = $this->metaOptions->withTypesManipulator($manipulator->reveal());

        self::assertNotSame($this->metaOptions, $new);
        self::assertSame($manipulator->reveal(), $new->getTypesManipulator());
    }

    /** @test */
    public function it_is_possible_to_change_method_manipulator(): void
    {
        $manipulator = $this->prophesize(MethodsManipulatorInterface::class);
        $new = $this->metaOptions->withMethodsManipulator($manipulator->reveal());

        self::assertNotSame($this->metaOptions, $new);
        self::assertSame($manipulator->reveal(), $new->getMethodsManipulator());
    }

    public function it_can_be_configured_from_constructor(): void
    {
        $methodsManipulator = $this->prophesize(MethodsManipulatorInterface::class);
        $typesManipulator = $this->prophesize(TypesManipulatorInterface::class);
        $metaOptions = new MetadataOptions($methodsManipulator->reveal(), $typesManipulator->reveal());

        self::assertSame($typesManipulator->reveal(), $metaOptions->getTypesManipulator());
        self::assertSame($methodsManipulator->reveal(), $metaOptions->getMethodsManipulator());
    }
}
