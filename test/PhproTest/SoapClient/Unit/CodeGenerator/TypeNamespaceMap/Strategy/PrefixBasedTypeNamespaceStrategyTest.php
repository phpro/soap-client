<?php

namespace PhproTest\SoapClient\Unit\CodeGenerator\TypeNamespaceMap\Strategy;

use Phpro\SoapClient\CodeGenerator\Config\Destination;
use Phpro\SoapClient\CodeGenerator\TypeNamespaceMap\Strategy\PrefixBasedTypeNamespaceStrategy;
use Phpro\SoapClient\CodeGenerator\TypeNamespaceMap\Strategy\TypeNamespaceMapStrategyInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

class PrefixBasedTypeNamespaceStrategyTest extends TestCase
{
    #[Test]
    public function it_implements_the_strategy_interface(): void
    {
        $strategy = new PrefixBasedTypeNamespaceStrategy();
        $this->assertInstanceOf(TypeNamespaceMapStrategyInterface::class, $strategy);
    }

    #[Test]
    public function it_appends_normalized_prefix_to_fallback(): void
    {
        $strategy = new PrefixBasedTypeNamespaceStrategy();
        $fallback = new Destination('src/Type', 'App\\Type');

        $result = $strategy('http://www.opengis.net/gml/3.2', 'gml', $fallback);

        $this->assertEquals(new Destination('src/Type/Gml', 'App\\Type\\Gml'), $result);
    }

    #[Test]
    public function it_returns_fallback_when_prefix_is_empty(): void
    {
        $strategy = new PrefixBasedTypeNamespaceStrategy();
        $fallback = new Destination('src/Type', 'App\\Type');

        $result = $strategy('http://example.com/schema', '', $fallback);

        $this->assertSame($fallback, $result);
    }

    #[Test]
    public function it_normalizes_hyphenated_prefixes(): void
    {
        $strategy = new PrefixBasedTypeNamespaceStrategy();
        $fallback = new Destination('src/Type', 'App\\Type');

        $result = $strategy('http://example.com/schema', 'my-prefix', $fallback);

        $this->assertEquals(new Destination('src/Type/MyPrefix', 'App\\Type\\MyPrefix'), $result);
    }

    #[Test]
    public function it_prefixes_leading_digit_segments(): void
    {
        $strategy = new PrefixBasedTypeNamespaceStrategy();
        $fallback = new Destination('src/Type', 'App\\Type');

        $result = $strategy('http://example.com/schema', '3d', $fallback);

        $this->assertEquals(new Destination('src/Type/Ns3d', 'App\\Type\\Ns3d'), $result);
    }
}
