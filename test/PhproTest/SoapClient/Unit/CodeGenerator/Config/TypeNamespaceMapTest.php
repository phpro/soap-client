<?php

namespace PhproTest\SoapClient\Unit\CodeGenerator\Config;

use Phpro\SoapClient\CodeGenerator\Config\Destination;
use Phpro\SoapClient\CodeGenerator\Config\TypeNamespaceMap;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Soap\Engine\Metadata\Model\XsdType;

class TypeNamespaceMapTest extends TestCase
{
    #[Test]
    public function it_can_be_created_with_fallback_destination(): void
    {
        $fallback = new Destination('/src/Type', 'App\\Type');
        $map = TypeNamespaceMap::create($fallback);

        $this->assertInstanceOf(TypeNamespaceMap::class, $map);
    }

    #[Test]
    public function it_returns_fallback_destination_when_no_mapping_exists(): void
    {
        $fallback = new Destination('/src/Type', 'App\\Type');
        $map = TypeNamespaceMap::create($fallback);

        $type = XsdType::create('SomeType')->withXmlNamespace('http://example.com/schema');
        $destination = $map->detectDestinationForType($type);

        $this->assertSame($fallback, $destination);
    }

    #[Test]
    public function it_can_add_mapping_for_xml_namespace(): void
    {
        $fallback = new Destination('/src/Type', 'App\\Type');
        $customDestination = new Destination('/src/Custom', 'App\\Custom');

        $map = TypeNamespaceMap::create($fallback)
            ->withMapping('http://custom.example.com', $customDestination);

        $type = XsdType::create('CustomType')->withXmlNamespace('http://custom.example.com');
        $destination = $map->detectDestinationForType($type);

        $this->assertSame($customDestination, $destination);
    }

    #[Test]
    public function it_can_add_multiple_mappings(): void
    {
        $fallback = new Destination('/src/Type', 'App\\Type');
        $custom1 = new Destination('/src/Custom1', 'App\\Custom1');
        $custom2 = new Destination('/src/Custom2', 'App\\Custom2');

        $map = TypeNamespaceMap::create($fallback)
            ->withMapping('http://custom1.example.com', $custom1)
            ->withMapping('http://custom2.example.com', $custom2);

        $type1 = XsdType::create('Type1')->withXmlNamespace('http://custom1.example.com');
        $type2 = XsdType::create('Type2')->withXmlNamespace('http://custom2.example.com');

        $this->assertSame($custom1, $map->detectDestinationForType($type1));
        $this->assertSame($custom2, $map->detectDestinationForType($type2));
    }

    #[Test]
    public function it_returns_fallback_when_type_has_no_xml_namespace(): void
    {
        $fallback = new Destination('/src/Type', 'App\\Type');
        $custom = new Destination('/src/Custom', 'App\\Custom');

        $map = TypeNamespaceMap::create($fallback)
            ->withMapping('http://custom.example.com', $custom);

        $type = XsdType::create('TypeWithoutNamespace');
        $destination = $map->detectDestinationForType($type);

        $this->assertSame($fallback, $destination);
    }

    #[Test]
    public function it_returns_fallback_when_xml_namespace_is_not_mapped(): void
    {
        $fallback = new Destination('/src/Type', 'App\\Type');
        $custom = new Destination('/src/Custom', 'App\\Custom');

        $map = TypeNamespaceMap::create($fallback)
            ->withMapping('http://custom.example.com', $custom);

        $type = XsdType::create('OtherType')->withXmlNamespace('http://other.example.com');
        $destination = $map->detectDestinationForType($type);

        $this->assertSame($fallback, $destination);
    }

    #[Test]
    public function it_is_immutable_when_adding_mappings(): void
    {
        $fallback = new Destination('/src/Type', 'App\\Type');
        $custom = new Destination('/src/Custom', 'App\\Custom');

        $map1 = TypeNamespaceMap::create($fallback);
        $map2 = $map1->withMapping('http://custom.example.com', $custom);

        // Original map should not have the mapping
        $type = XsdType::create('Type')->withXmlNamespace('http://custom.example.com');
        $this->assertSame($fallback, $map1->detectDestinationForType($type));

        // New map should have the mapping
        $this->assertSame($custom, $map2->detectDestinationForType($type));
    }

    #[Test]
    public function it_can_handle_real_world_xml_namespaces(): void
    {
        $fallback = new Destination('/src/Type', 'App\\Type');
        $xoevDestination = new Destination('/src/Type/Xoev', 'App\\Type\\Xoev');
        $gmlDestination = new Destination('/src/Type/Gml', 'App\\Type\\Gml');

        $map = TypeNamespaceMap::create($fallback)
            ->withMapping('http://xoev.de/schemata/xzufi/2_2_0', $xoevDestination)
            ->withMapping('http://www.opengis.net/gml/3.2', $gmlDestination);

        $xoevType = XsdType::create('XZuFiType')
            ->withXmlNamespace('http://xoev.de/schemata/xzufi/2_2_0');
        $gmlType = XsdType::create('PointType')
            ->withXmlNamespace('http://www.opengis.net/gml/3.2');
        $standardType = XsdType::create('StandardType')
            ->withXmlNamespace('http://example.com/standard');

        $this->assertSame($xoevDestination, $map->detectDestinationForType($xoevType));
        $this->assertSame($gmlDestination, $map->detectDestinationForType($gmlType));
        $this->assertSame($fallback, $map->detectDestinationForType($standardType));
    }

    #[Test]
    public function it_can_override_existing_mapping(): void
    {
        $fallback = new Destination('/src/Type', 'App\\Type');
        $custom1 = new Destination('/src/Custom1', 'App\\Custom1');
        $custom2 = new Destination('/src/Custom2', 'App\\Custom2');

        $map = TypeNamespaceMap::create($fallback)
            ->withMapping('http://custom.example.com', $custom1)
            ->withMapping('http://custom.example.com', $custom2); // Override

        $type = XsdType::create('Type')->withXmlNamespace('http://custom.example.com');
        $destination = $map->detectDestinationForType($type);

        $this->assertSame($custom2, $destination);
    }

    #[Test]
    public function it_can_add_a_strategy(): void
    {
        $fallback = new Destination('/src/Type', 'App\\Type');
        $strategyDestination = new Destination('/src/Strategy', 'App\\Strategy');

        $map = TypeNamespaceMap::create($fallback)
            ->withStrategy(fn (string $xmlns, Destination $fallback) => $strategyDestination);

        $type = XsdType::create('SomeType')->withXmlNamespace('http://example.com/schema');
        $destination = $map->detectDestinationForType($type);

        $this->assertSame($strategyDestination, $destination);
    }

    #[Test]
    public function it_does_not_call_strategy_when_xmlns_is_in_map(): void
    {
        $fallback = new Destination('/src/Type', 'App\\Type');
        $mapped = new Destination('/src/Mapped', 'App\\Mapped');
        $strategyCalled = false;

        $map = TypeNamespaceMap::create($fallback)
            ->withMapping('http://mapped.example.com', $mapped)
            ->withStrategy(function (string $xmlns, Destination $fallback) use (&$strategyCalled) {
                $strategyCalled = true;
                return new Destination('/src/Strategy', 'App\\Strategy');
            });

        $type = XsdType::create('MappedType')->withXmlNamespace('http://mapped.example.com');
        $destination = $map->detectDestinationForType($type);

        $this->assertSame($mapped, $destination);
        $this->assertFalse($strategyCalled);
    }

    #[Test]
    public function it_calls_strategy_when_xmlns_is_not_in_map(): void
    {
        $fallback = new Destination('/src/Type', 'App\\Type');
        $mapped = new Destination('/src/Mapped', 'App\\Mapped');
        $strategyDestination = new Destination('/src/Strategy', 'App\\Strategy');
        $strategyCalled = false;

        $map = TypeNamespaceMap::create($fallback)
            ->withMapping('http://mapped.example.com', $mapped)
            ->withStrategy(function (string $xmlns, Destination $fallback) use (&$strategyCalled, $strategyDestination) {
                $strategyCalled = true;
                return $strategyDestination;
            });

        $type = XsdType::create('UnmappedType')->withXmlNamespace('http://unmapped.example.com');
        $destination = $map->detectDestinationForType($type);

        $this->assertSame($strategyDestination, $destination);
        $this->assertTrue($strategyCalled);
    }

    #[Test]
    public function it_passes_xmlns_and_fallback_to_strategy(): void
    {
        $fallback = new Destination('/src/Type', 'App\\Type');
        $receivedXmlns = null;
        $receivedFallback = null;

        $map = TypeNamespaceMap::create($fallback)
            ->withStrategy(function (string $xmlns, Destination $fb) use (&$receivedXmlns, &$receivedFallback) {
                $receivedXmlns = $xmlns;
                $receivedFallback = $fb;
                return $fb;
            });

        $type = XsdType::create('SomeType')->withXmlNamespace('http://example.com/schema');
        $map->detectDestinationForType($type);

        $this->assertSame('http://example.com/schema', $receivedXmlns);
        $this->assertSame($fallback, $receivedFallback);
    }

    #[Test]
    public function it_returns_fallback_when_no_strategy_and_xmlns_not_in_map(): void
    {
        $fallback = new Destination('/src/Type', 'App\\Type');

        $map = TypeNamespaceMap::create($fallback);

        $type = XsdType::create('SomeType')->withXmlNamespace('http://example.com/schema');
        $destination = $map->detectDestinationForType($type);

        $this->assertSame($fallback, $destination);
    }

    #[Test]
    public function it_can_use_strategy_to_calculate_destination_based_on_xmlns(): void
    {
        $fallback = new Destination('/src/Type', 'App\\Type');

        $map = TypeNamespaceMap::create($fallback)
            ->withStrategy(function (string $xmlns, Destination $fallback): Destination {
                if (str_contains($xmlns, 'xoev.de')) {
                    return new Destination('/src/Type/Xoev', 'App\\Type\\Xoev');
                }

                return $fallback;
            });

        $xoevType = XsdType::create('XZuFiType')
            ->withXmlNamespace('http://xoev.de/schemata/xzufi/2_2_0');
        $otherType = XsdType::create('OtherType')
            ->withXmlNamespace('http://example.com/other');

        $this->assertEquals(new Destination('/src/Type/Xoev', 'App\\Type\\Xoev'), $map->detectDestinationForType($xoevType));
        $this->assertSame($fallback, $map->detectDestinationForType($otherType));
    }

    #[Test]
    public function it_can_remove_strategy_by_setting_null(): void
    {
        $fallback = new Destination('/src/Type', 'App\\Type');
        $strategyDestination = new Destination('/src/Strategy', 'App\\Strategy');

        $mapWithStrategy = TypeNamespaceMap::create($fallback)
            ->withStrategy(fn (string $xmlns, Destination $fallback) => $strategyDestination);

        $mapWithoutStrategy = $mapWithStrategy->withStrategy(null);

        $type = XsdType::create('SomeType')->withXmlNamespace('http://example.com/schema');

        $this->assertSame($strategyDestination, $mapWithStrategy->detectDestinationForType($type));
        $this->assertSame($fallback, $mapWithoutStrategy->detectDestinationForType($type));
    }

    #[Test]
    public function it_is_immutable_when_adding_strategy(): void
    {
        $fallback = new Destination('/src/Type', 'App\\Type');
        $strategyDestination = new Destination('/src/Strategy', 'App\\Strategy');

        $map1 = TypeNamespaceMap::create($fallback);
        $map2 = $map1->withStrategy(fn (string $xmlns, Destination $fallback) => $strategyDestination);

        $type = XsdType::create('SomeType')->withXmlNamespace('http://example.com/schema');

        // Original map should not use the strategy
        $this->assertSame($fallback, $map1->detectDestinationForType($type));

        // New map should use the strategy
        $this->assertSame($strategyDestination, $map2->detectDestinationForType($type));
    }
}
