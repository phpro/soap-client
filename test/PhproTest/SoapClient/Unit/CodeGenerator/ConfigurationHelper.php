<?php

declare(strict_types=1);

namespace PhproTest\SoapClient\Unit\CodeGenerator;

use Phpro\SoapClient\CodeGenerator\Config\ClassMapConfig;
use Phpro\SoapClient\CodeGenerator\Config\ClientConfig;
use Phpro\SoapClient\CodeGenerator\Config\Destination;
use Phpro\SoapClient\CodeGenerator\Config\TypeNamespaceMap;

trait ConfigurationHelper
{
    private function createTypeNamespaceMap(string $namespace = 'Generated'): TypeNamespaceMap
    {
        return TypeNamespaceMap::create(new Destination('/generated', $namespace));
    }

    private function createDestination(string $path = '/generated', string $namespace = 'Generated'): Destination
    {
        return new Destination($path, $namespace);
    }

    private function createClientConfig(string $name = 'TestClient', string $namespace = 'Generated'): ClientConfig
    {
        return new ClientConfig($name, new Destination('/generated', $namespace));
    }

    private function createClassMapConfig(string $name = 'TestClassMap', string $namespace = 'Generated'): ClassMapConfig
    {
        return new ClassMapConfig($name, new Destination('/generated', $namespace));
    }
}
