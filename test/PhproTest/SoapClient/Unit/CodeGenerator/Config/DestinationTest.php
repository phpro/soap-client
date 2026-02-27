<?php

namespace PhproTest\SoapClient\Unit\CodeGenerator\Config;

use Phpro\SoapClient\CodeGenerator\Config\Destination;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

class DestinationTest extends TestCase
{
    #[Test]
    public function it_can_be_constructed_with_path_and_namespace(): void
    {
        $destination = new Destination('/src/Type', 'App\\Type');

        $this->assertSame('/src/Type', $destination->path);
        $this->assertSame('App\\Type', $destination->namespace);
    }

    #[Test]
    public function it_can_handle_nested_namespaces(): void
    {
        $destination = new Destination('/src/Type/Deep/Nested', 'App\\Type\\Deep\\Nested');

        $this->assertSame('/src/Type/Deep/Nested', $destination->path);
        $this->assertSame('App\\Type\\Deep\\Nested', $destination->namespace);
    }

    #[Test]
    public function it_can_handle_root_namespace(): void
    {
        $destination = new Destination('/src', '\\');

        $this->assertSame('/src', $destination->path);
        $this->assertSame('\\', $destination->namespace);
    }

    #[Test]
    public function it_can_handle_single_level_namespace(): void
    {
        $destination = new Destination('/src/Type', 'Type');

        $this->assertSame('/src/Type', $destination->path);
        $this->assertSame('Type', $destination->namespace);
    }
}
