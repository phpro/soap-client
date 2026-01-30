<?php

namespace PhproTest\SoapClient\Unit\CodeGenerator\Config;

use Phpro\SoapClient\CodeGenerator\Config\ClientConfig;
use Phpro\SoapClient\CodeGenerator\Config\Destination;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ClientConfigTest extends TestCase
{
    #[Test]
    public function it_can_be_constructed_with_name_and_destination(): void
    {
        $destination = new Destination('/src/Client', 'App\\Client');
        $config = new ClientConfig('MyClient', $destination);

        $this->assertSame('MyClient', $config->name);
        $this->assertSame($destination, $config->destination);
    }

    #[Test]
    public function it_generates_correct_file_path(): void
    {
        $destination = new Destination('/src/Client', 'App\\Client');
        $config = new ClientConfig('MyClient', $destination);

        $this->assertSame('/src/Client/MyClient.php', $config->path());
    }

    #[Test]
    public function it_generates_correct_fully_qualified_class_name(): void
    {
        $destination = new Destination('/src/Client', 'App\\Client');
        $config = new ClientConfig('MyClient', $destination);

        $this->assertSame('App\\Client\\MyClient', $config->fqcn());
    }

    #[Test]
    public function it_handles_nested_namespaces(): void
    {
        $destination = new Destination('/src/Client/Api/V1', 'App\\Client\\Api\\V1');
        $config = new ClientConfig('SoapClient', $destination);

        $this->assertSame('/src/Client/Api/V1/SoapClient.php', $config->path());
        $this->assertSame('App\\Client\\Api\\V1\\SoapClient', $config->fqcn());
    }

    #[Test]
    public function it_handles_simple_namespace(): void
    {
        $destination = new Destination('/src', 'App');
        $config = new ClientConfig('Client', $destination);

        $this->assertSame('/src/Client.php', $config->path());
        $this->assertSame('App\\Client', $config->fqcn());
    }

    #[Test]
    public function it_handles_path_without_leading_slash(): void
    {
        $destination = new Destination('src/Client', 'App\\Client');
        $config = new ClientConfig('MyClient', $destination);

        $this->assertSame('src/Client/MyClient.php', $config->path());
    }

    #[Test]
    public function it_handles_trailing_slash_in_path(): void
    {
        $destination = new Destination('/src/Client/', 'App\\Client');
        $config = new ClientConfig('MyClient', $destination);

        $this->assertSame('/src/Client/MyClient.php', $config->path());
    }
}
