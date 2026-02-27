<?php

namespace PhproTest\SoapClient\Unit\CodeGenerator\Config;

use Phpro\SoapClient\CodeGenerator\Config\ClassMapConfig;
use Phpro\SoapClient\CodeGenerator\Config\Destination;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ClassMapConfigTest extends TestCase
{
    #[Test]
    public function it_can_be_constructed_with_name_and_destination(): void
    {
        $destination = new Destination('/src/Classmap', 'App\\Classmap');
        $config = new ClassMapConfig('MyClassmap', $destination);

        $this->assertSame('MyClassmap', $config->name);
        $this->assertSame($destination, $config->destination);
    }

    #[Test]
    public function it_generates_correct_file_path(): void
    {
        $destination = new Destination('/src/Classmap', 'App\\Classmap');
        $config = new ClassMapConfig('MyClassmap', $destination);

        $this->assertSame('/src/Classmap/MyClassmap.php', $config->path());
    }

    #[Test]
    public function it_generates_correct_fully_qualified_class_name(): void
    {
        $destination = new Destination('/src/Classmap', 'App\\Classmap');
        $config = new ClassMapConfig('MyClassmap', $destination);

        $this->assertSame('App\\Classmap\\MyClassmap', $config->fqcn());
    }

    #[Test]
    public function it_handles_nested_namespaces(): void
    {
        $destination = new Destination('/src/Classmap/Generated/V1', 'App\\Classmap\\Generated\\V1');
        $config = new ClassMapConfig('SoapClassmap', $destination);

        $this->assertSame('/src/Classmap/Generated/V1/SoapClassmap.php', $config->path());
        $this->assertSame('App\\Classmap\\Generated\\V1\\SoapClassmap', $config->fqcn());
    }

    #[Test]
    public function it_handles_simple_namespace(): void
    {
        $destination = new Destination('/src', 'App');
        $config = new ClassMapConfig('Classmap', $destination);

        $this->assertSame('/src/Classmap.php', $config->path());
        $this->assertSame('App\\Classmap', $config->fqcn());
    }

    #[Test]
    public function it_handles_path_without_leading_slash(): void
    {
        $destination = new Destination('src/Classmap', 'App\\Classmap');
        $config = new ClassMapConfig('MyClassmap', $destination);

        $this->assertSame('src/Classmap/MyClassmap.php', $config->path());
    }

    #[Test]
    public function it_handles_trailing_slash_in_path(): void
    {
        $destination = new Destination('/src/Classmap/', 'App\\Classmap');
        $config = new ClassMapConfig('MyClassmap', $destination);

        $this->assertSame('/src/Classmap/MyClassmap.php', $config->path());
    }
}
