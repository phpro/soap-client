# Configuration

The code generation commands require a configuration file to determine how the SOAP classes need to be generated.

```php
<?php
// my-soap-config.php

use Phpro\SoapClient\CodeGenerator\Assembler;
use Phpro\SoapClient\CodeGenerator\Rules;
use Phpro\SoapClient\CodeGenerator\Config\ClassMapConfig;
use Phpro\SoapClient\CodeGenerator\Config\ClientConfig;
use Phpro\SoapClient\CodeGenerator\Config\Config;
use Phpro\SoapClient\CodeGenerator\Config\Destination;
use Phpro\SoapClient\CodeGenerator\Config\TypeNamespaceMap;
use Phpro\SoapClient\Soap\EngineOptions;
use Phpro\SoapClient\Soap\DefaultEngineFactory;

return Config::create()
    ->setEngine(DefaultEngineFactory::create(
        EngineOptions::defaults($wsdl)
            ->withWsdlLoader(new FlatteningLoader(new StreamWrapperLoader()))
            ->withEncoderRegistry(
                EncoderRegistry::default()
                    ->addClassMapCollection(SomeClassmap::types())
                    ->addBackedEnumClassMapCollection(SomeClassmap::enums())
            )
    ))
    ->setTypeNamespaceMap(
        TypeNamespaceMap::create(new Destination('SoapTypes', 'src/SoapTypes'))
            // You can add specific XML xmlns -> PHP namespace mappings here:
            // If no mapping is found, the default destination + namespace will be used.
            ->withMapping('http://www.xmlns.mapping', new Destination('src/Type/OtherDir', 'App\\Type\\OtherDir'))
    )
    ->setClient(new ClientConfig('MySoapClient', new Destination('SoapClient', 'src/SoapClient')))
    ->setClassMap(new ClassMapConfig('AcmeClassmap', new Destination('Acme\\Classmap', 'src/acme/classmap')))
    ->addRule(new Rules\AssembleRule(new Assembler\GetterAssembler(new Assembler\GetterAssemblerOptions())))
    ->addRule(new Rules\AssembleRule(new Assembler\ImmutableSetterAssembler(
        new Assembler\ImmutableSetterAssemblerOptions()
    )))
    ->addRule(
        new Rules\IsRequestRule(
            $engine->getMetadata(),
            new Rules\MultiRule([
                new Rules\AssembleRule(new Assembler\RequestAssembler()),
                new Rules\AssembleRule(new Assembler\ConstructorAssembler(new Assembler\ConstructorAssemblerOptions())),
            ])
        )
    )
    ->addRule(
        new Rules\IsResultRule(
            $engine->getMetadata(),
            new Rules\MultiRule([
                new Rules\AssembleRule(new Assembler\ResultAssembler()),
            ])
        )
    )
    ->addRule(
        new Rules\IsExtendingTypeRule(
            $engine->getMetadata(),
            new Rules\AssembleRule(new Assembler\ExtendingTypeAssembler())
        )
    )
    ->addRule(
        new Rules\IsAbstractTypeRule(
            $engine->getMetadata(),
            new Rules\AssembleRule(new Assembler\AbstractClassAssembler())
        )
    )
;
```

Luckily a command is provided to generate this for you in an interactive manner.
Execute `vendor/bin/soap-client generate:config` to start the interactive config generator.

**engine**

`Soap\Engine\Engine` - REQUIRED

Specify how the code generation tool can talk to SOAP.
By default, we push a custom engine that deeply parses the WSDL for code generation purpose.
For loading the WSDL, a stream based WSDL loader is being used in 'flattening' mode.
It is possible to change this to any other configuration you want to use
and provide additional options like the preferred SOAP version.

[Read more about engines.](https://github.com/php-soap/engine)

```php
use Phpro\SoapClient\Soap\EngineOptions;
use Phpro\SoapClient\Soap\DefaultEngineFactory;

DefaultEngineFactory::create(
    EngineOptions::defaults($wsdl)
        ->withWsdlLoader(new FlatteningLoader(new StreamWrapperLoader()))
        ->withEncoderRegistry(
            EncoderRegistry::default()
                ->addClassMapCollection(SomeClassmap::types())
                ->addBackedEnumClassMapCollection(SomeClassmap::enums())
        )
        // If you want to enable WSDL caching:
        // ->withCache() 
        // If you want to use Alternate HTTP settings:
        // ->withWsdlLoader()
        // ->withTransport()
        // If you want specific SOAP setting:
        // ->withWsdlParserContext()
        // ->withWsdlServiceSelectionCriteria()
);
```

**Type Namespace Map**

Use `setTypeNamespaceMap(TypeNamespaceMap::create($namespace, $destination))` to configure the namespace and destination for generated types.

You can also add specific XML namespace to PHP namespace mappings by using the `withMapping($xmlNamespace, Destination)` method on the created TypeNamespaceMap instance.

**Client Configuration**

Use `setClient(new ClientConfig($name, $destination))` to configure the generated client class.

**Classmap Configuration**

Use `setClassMap(new ClassMapConfig($name, $destination))` to configure the classmap.

**rules**

RuleInterface - OPTIONAL

You can specify how you want to generate your code.
More information about the topic is available in the [rules](rules.md) and [assemblers](assemblers.md) section.

The pre-defined rules are override-able by calling `setRuleSet` on the constructed object.

For example, to make all your properties protected:
```php
Config::create()
    ->setRuleSet(
        new Rules\RuleSet(
            [
                new Rules\AssembleRule(new Assembler\PropertyAssembler(Assembler\PropertyAssemblerOptions::create()->withVisibility(PropertyGenerator::VISIBILITY_PROTECTED))),
                new Rules\AssembleRule(new Assembler\ClassMapAssembler()),
            ]
        )
    )
```

**Metadata manipulations**

The metadata manipulations are a set of strategies that can be applied to the metadata before the code generation starts.
You can read more about this in the documentation in the section [metadata](../drivers/metadata.md).

Examples:

```php
use Phpro\SoapClient\CodeGenerator\Config\Config;
use Phpro\SoapClient\Soap\Metadata\Manipulators\DuplicateTypes\IntersectDuplicateTypesStrategy;
use Phpro\SoapClient\Soap\Metadata\Manipulators\DuplicateTypes\RemoveDuplicateTypesStrategy;
use Phpro\SoapClient\Soap\Metadata\Manipulators\TypeReplacer\TypeReplacers;

Config::create()
    // Use the factory method - this is namespace-aware when TypeNamespaceMap is configured
    ->setDuplicateTypeIntersectStrategy(IntersectDuplicateTypesStrategy::create())
    // Or use the remove strategy instead:
    // ->setDuplicateTypeIntersectStrategy(RemoveDuplicateTypesStrategy::create())
    ->setTypeReplacementStrategy(TypeReplacers::defaults()->add(new MyDateReplacer()));
```

The duplicate type strategies use a factory pattern (`create()`) that returns a closure.
This closure receives the `TypeNamespaceMap` from the configuration, enabling namespace-aware duplicate detection.
When types map to different PHP namespaces, they are not considered duplicates.

**Enumeration options**

You can configure how the code generator deals with XSD enumeration types.
There are 2 type of XSD enumerations: 

- `global`: Are available as a global simpletype inside the XSD.
- `local`: Are configured as an internal type on an element or attribute and don't really have a name.

The default behavior is to generate a PHP Enum for global enumerations only because
We want to avoid naming conflicts with other types for local enumerations. 

It is possible to opt-in into using these local enumerations as well:

```php
use Phpro\SoapClient\CodeGenerator\Config\Config;
use Phpro\SoapClient\CodeGenerator\Config\EnumerationGenerationStrategy;

Config::create()
    ->setEnumerationGenerationStrategy(EnumerationGenerationStrategy::LocalAndGlobal);
```

**Note**: This will dynamically add some extra type replacements and type manipulations to the metadata before the code generation starts.
