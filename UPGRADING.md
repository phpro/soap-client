# V4 to V5

## Configuration overhaul

The configuration system has been significantly reworked.
The `Config` class no longer implements `ConfigInterface` (which has been removed) and uses new value objects for configuring destinations.

### New value objects

Individual string-based configuration (name, namespace, destination) has been replaced by dedicated value objects:

- **`Destination`**: Combines a `path` and `namespace` into a single object.
- **`ClientConfig`**: Wraps a client name and `Destination`.
- **`ClassMapConfig`**: Wraps a classmap name and `Destination`.
- **`TypeNamespaceMap`**: Replaces the old `setTypeNamespace()` / `setTypeDestination()` combination.

### Migration

**Before (v4):**

```php
use Phpro\SoapClient\CodeGenerator\Config\Config;

return Config::create()
    ->setEngine($engine = DefaultEngineFactory::create(
        EngineOptions::defaults('your.wsdl')
    ))
    ->setTypeDestination('src/Type')
    ->setTypeNamespace('App\\Type')
    ->setClientName('MyClient')
    ->setClientNamespace('App')
    ->setClientDestination('src')
    ->setClassMapName('MyClassmap')
    ->setClassMapNamespace('App')
    ->setClassMapDestination('src');
```

**After (v5):**

```php
use Phpro\SoapClient\CodeGenerator\Config\ClassMapConfig;
use Phpro\SoapClient\CodeGenerator\Config\ClientConfig;
use Phpro\SoapClient\CodeGenerator\Config\Config;
use Phpro\SoapClient\CodeGenerator\Config\Destination;
use Phpro\SoapClient\CodeGenerator\Config\TypeNamespaceMap;

return Config::create()
    ->setEngine($engine = DefaultEngineFactory::create(
        EngineOptions::defaults('your.wsdl')
    ))
    ->setTypeNamespaceMap(
        TypeNamespaceMap::create(new Destination('src/Type', 'App\\Type'))
    )
    ->setClient(new ClientConfig('MyClient', new Destination('src', 'App')))
    ->setClassMap(new ClassMapConfig('MyClassmap', new Destination('src', 'App')));
```

### Removed methods on `Config`

| Removed method | Replacement |
|---|---|
| `setTypeDestination()` | `setTypeNamespaceMap(TypeNamespaceMap::create(new Destination(...)))` |
| `setTypeNamespace()` | (included in `Destination`) |
| `getTypeDestination()` | `getTypeNamespaceMap()` |
| `getTypeNamespace()` | (included in `TypeNamespaceMap`) |
| `setClientName()` | `setClient(new ClientConfig(...))` |
| `setClientNamespace()` | (included in `ClientConfig`) |
| `setClientDestination()` | (included in `ClientConfig`) |
| `getClientName()` | `getClient()->name` |
| `getClientNamespace()` | `getClient()->destination->namespace` |
| `getClientDestination()` | `getClient()->destination->path` |
| `setClassMapName()` | `setClassMap(new ClassMapConfig(...))` |
| `setClassMapNamespace()` | (included in `ClassMapConfig`) |
| `setClassMapDestination()` | (included in `ClassMapConfig`) |
| `getClassMapName()` | `getClassMap()->name` |
| `getClassMapNamespace()` | `getClassMap()->destination->namespace` |
| `getClassMapDestination()` | `getClassMap()->destination->path` |

### `ConfigInterface` removed

The `ConfigInterface` has been removed entirely. If you had a custom configuration class implementing `ConfigInterface`, you must now use the `Config` class directly.

## TypeNamespaceMap: XML namespace mapping and strategies

The `TypeNamespaceMap` allows you to map XML namespaces (xmlns) to specific PHP namespace destinations.
This is useful when a WSDL defines types across multiple XML namespaces and you want to organize them into separate PHP directories/namespaces.

### Explicit mappings

```php
TypeNamespaceMap::create(new Destination('src/Type', 'App\\Type'))
    ->withMapping('http://www.opengis.net/gml/3.2', new Destination('src/Type/Gml', 'App\\Type\\Gml'))
    ->withMapping('http://xoev.de/schemata/xzufi/2_2_0', new Destination('src/Type/Xzufi', 'App\\Type\\Xzufi'))
```

### Strategy-based resolution

Instead of manually mapping every xmlns, you can use a strategy to automatically resolve destinations.
A built-in `PrefixBasedTypeNamespaceStrategy` derives a sub-namespace from the XML namespace prefix:

```php
use Phpro\SoapClient\CodeGenerator\TypeNamespaceMap\Strategy\PrefixBasedTypeNamespaceStrategy;

TypeNamespaceMap::create(new Destination('src/Type', 'App\\Type'))
    ->withStrategy(new PrefixBasedTypeNamespaceStrategy())
```

With this strategy, a type in the `gml` xmlns prefix is automatically placed in `src/Type/Gml` with namespace `App\Type\Gml`.

Explicit `withMapping()` entries always take precedence over the strategy. You can combine both:

```php
TypeNamespaceMap::create(new Destination('src/Type', 'App\\Type'))
    ->withMapping('http://special.example.com', new Destination('src/Type/Special', 'App\\Type\\Special'))
    ->withStrategy(new PrefixBasedTypeNamespaceStrategy())
```

You can also implement a custom strategy via `TypeNamespaceMapStrategyInterface` or pass any callable.

### Duplicate type strategies are now namespace-aware

The `IntersectDuplicateTypesStrategy` and `RemoveDuplicateTypesStrategy` now use a factory pattern (`create()`) that accepts the `TypeNamespaceMap`. When types map to different PHP namespaces, they are not considered duplicates.

## Interactive config generator improvements

The `generate:config` command now attempts to load the WSDL and detect XML namespaces automatically.
Detected namespaces are included as commented-out `withMapping()` suggestions and a commented-out `withStrategy()` line in the generated configuration file.
If you want to have this mapping in your configuration, you can start out fresh by running:

```
./vendor/bin/soap-client generate:config --config=config/soap-client.php
```

## Constructor and property default values

The `withDefaultValues()` method on both `ConstructorAssemblerOptions` and `PropertyAssemblerOptions` now accepts a `DefaultValuesStrategy`:

```php
use Phpro\SoapClient\CodeGenerator\Config\DefaultValuesStrategy;

(new ConstructorAssemblerOptions())->withDefaultValues(DefaultValuesStrategy::OptionalOnly)
(new ConstructorAssemblerOptions())->withDefaultValues(DefaultValuesStrategy::All)
(new ConstructorAssemblerOptions())->withDefaultValues(DefaultValuesStrategy::None)
```

The three strategies are:
- **`OptionalOnly`** (new default): Only WSDL-optional/nullable properties get `= null`. Required properties have no default, preventing silent construction without providing them.
- **`All`** (previous default): All scalar types get zero-value defaults (`''`, `0`, `false`, `0.0`, `[]`), nullable types get `= null`.
- **`None`**: No defaults are applied.

The `ConstructorAssembler` also supports a `withOptionalValue()` option that forces all parameters to `?Type = null`, matching the `PropertyAssembler` behavior.

The `PropertyDefaultsAssembler` has been removed. Its functionality is now built into `PropertyAssembler` via `PropertyAssemblerOptions::create()->withDefaultValues()`. To restore the old behavior where all scalars get defaults:

```php
new PropertyAssembler(PropertyAssemblerOptions::create()->withDefaultValues(DefaultValuesStrategy::All))
```

## Regenerate classes

After upgrading, regenerate all your classes:

```
./vendor/bin/soap-client generate:client --config=config/soap-client.php
./vendor/bin/soap-client generate:classmap --config=config/soap-client.php
./vendor/bin/soap-client generate:types --config=config/soap-client.php
./vendor/bin/soap-client generate:clientfactory --config=config/soap-client.php
```

**Note:** The generated code may have changed for your project due to the namespace-aware type generation. Validate that you are still using the correct methods in your implementation.

---

# V3 to V4

**NOTE:** We now require a `psr/cache-implementation` so that the engine (and WSDL parsing) can be cached. Some examples are: `symfony/cache` or `cache/*-adapter`.
If you don't have a cache implementation installed, you can for example run:

```bash
composer require symfony/cache
```

Next, you can upgrade the soap-client:

```bash
composer require 'phpro/soap-client:^4.0.0' --update-with-dependencies
```

In V4, the engine factory changed again.
This will be the final breaking change for the engine factory since we now have a generic way to create it for both runtime and code generation:

Change the engine inside your code generation configuration:

```php
use Phpro\SoapClient\CodeGenerator\Config\Config;
use Phpro\SoapClient\Soap\CodeGeneratorEngineFactory;
use Phpro\SoapClient\Soap\DefaultEngineFactory;
use Phpro\SoapClient\Soap\EngineOptions;
use Soap\Wsdl\Loader\FlatteningLoader;
use Soap\Wsdl\Loader\StreamWrapperLoader;

return Config::create()
    ->setEngine($engine = DefaultEngineFactory::create(
        EngineOptions::defaults('your.wsdl')
            ->withWsdlLoader(new FlatteningLoader(new StreamWrapperLoader())) // Or a PSR18-based loader ... :))
    ))
```

**Note:** In case you are using a custom configuration class by implementing the `ConfigInterface` : this is not supported anymore.
Since code generation gets complexer and complexer, we decided to make the configuration more strict.

Regenerate classes:

```
./vendor/bin/soap-client generate:client --config=config/soap-client.php
./vendor/bin/soap-client generate:classmap --config=config/soap-client.php
./vendor/bin/soap-client generate:types --config=config/soap-client.php
./vendor/bin/soap-client generate:clientfactory --config=config/soap-client.php
```

**Note:** The generated code might have slightly changed for your project.
Validate if you are still using the correct methods in your implementation.

Change the engine inside your (generated) ClientFactory:

```php
$engine = DefaultEngineFactory::create(
    EngineOptions::defaults($wsdl)
        ->withEncoderRegistry(
            EncoderRegistry::default()
                ->addClassMapCollection(CalcClassmap::types())
                ->addBackedEnumClassMapCollection(CalcClassmap::enums())
        )
        // If you want to enable WSDL caching:
        // ->withCache($yourPsr6CachePool)
        // If you want to use Alternate HTTP settings:
        // ->withWsdlLoader()
        // ->withTransport()
        // If you want specific SOAP setting:
        // ->withWsdlParserContext()
        // ->withWsdlServiceSelectionCriteria()
);
```

[More information about the engine options can be found here.](/docs/cli/generate-clientfactory.md)


In case you are using a non-default metadata strategy, you can now configure it in the code generation configuration:

```php
use Phpro\SoapClient\CodeGenerator\Config\Config;
use Phpro\SoapClient\Soap\Metadata\Manipulators\DuplicateTypes\RemoveDuplicateTypesStrategy;

return Config::create()
    ->setDuplicateTypeIntersectStrategy(
        new RemoveDuplicateTypesStrategy()
    )
```

[More information about the metadata options can be found here.](/docs/drivers/metadata.md)


# V2 to V3

```bash
composer require 'phpro/soap-client:^3.0.0' --update-with-dependencies
```

Upgrading is a matter of changing the engine for code generation in the code-generation configuration file:

```php
use Phpro\SoapClient\Soap\CodeGeneratorEngineFactory;
use Soap\Wsdl\Loader\FlatteningLoader;
use Soap\Wsdl\Loader\StreamWrapperLoader;

return Config::create()
    ->setEngine($engine = CodeGeneratorEngineFactory::create(
        'your.wsdl',
        new FlatteningLoader(new StreamWrapperLoader()) // Or a PSR18-based loader ... :)
    ))
```

**Note:** You can still use the default engine, yet you won't get the information for the enhanced type generation.

Regenerate classes:

```
./vendor/bin/soap-client generate:client --config=config/soap-client.php
./vendor/bin/soap-client generate:classmap --config=config/soap-client.php
./vendor/bin/soap-client generate:types --config=config/soap-client.php
```

# V1 to V2

V1 has been around for quite some time.
Since the low-level SOAP parts became very stable and trustworthy,
We decided to move them [to a separate organisation](https://github.com/php-soap/).
This will make it easier to make changes in both this soap-client and the more low-level SOAP parts.
An additional benefit is that the robust SOAP fundamentals can be used by other SOAP clients as well.
We hope this will lead to an even more stable SOAP core. 

The focus of this package shifted to the generation of SOAP client code.
This is the part that makes it easy to interact with your SOAP service.
We decided to make it more opinionated, so that you have to deal with less strange SOAP related bugs than before.

By moving the low-level SOAP stuff out, we can now focus on improving WSDL metadata collection,
which will results in more strictly typed generated code.
You can expect this to come in one of the next releases.

## Prerequisites

If your application does not contain a PSR-18 client, you'll have to
choose which HTTP client you want to use.
This package expects some PSR implementations to be present in order to be installed:

* PSR-7: `psr/http-message-implementation` like `nyholm/psr7` or `guzzlehttp/psr7`
* PSR-17: `psr/http-factory-implementation` like `nyholm/psr7` or `guzzlehttp/psr7`
* PSR-18: `psr/http-client-implementation` like `symfony/http-client` or `guzzlehttp/guzzle`

Example implementations:

```
composer require symfony/http-client nyholm/psr7
```

## Upgrading

```bash
composer require phpro/soap-client:^2.0
```

We suggest you to use the `soap-client` CLI tools again if you want to upgrade and existing application:

```
./vendor/bin/soap-client wizard
```

From this point on, you can re-add the custom parts of your existing SOAP client back into the new SOAP client.

**You can check the updated documentation in order to discover how you need to do specific actions like adding middleware in v2.**


This update comes with some breaking changes:

## php-soap

The new php-soap organisation provides most of the interfaces that were previously in this package.

### Engine

https://github.com/php-soap/engine

Provides following interfaces:

* [Engine](https://raw.githubusercontent.com/php-soap/engine/main/src/Engine.php) : Replaces the old `EngineInterface`
* [Metadata](https://raw.githubusercontent.com/php-soap/engine/main/src/Metadata/Metadata.php) : Replaces the old `MetadataInterface`
* [MetadataProvider](https://raw.githubusercontent.com/php-soap/engine/main/src/Metadata/MetadataProvider.php) : Replaces the old `MetadataProviderInterface`
* [Driver](https://raw.githubusercontent.com/php-soap/engine/main/src/Driver.php) : Replaces the old `DriverInterface`
* [Decoder](https://raw.githubusercontent.com/php-soap/engine/main/src/Decoder.php) : Replaces the old `DecoderInterface`
* [Encoder](https://raw.githubusercontent.com/php-soap/engine/main/src/Encoder.php) : Replaces the old `EncoderInterface`


If you use any of these interfaces in your own code, you will need to replace them with the `php-soap` alternatives.


**Note:**

* The metadata has slightly been changed : property and parameter collections are replacing plain arrays.

### Transport

https://github.com/php-soap/psr18-transport/

* [Transport](https://raw.githubusercontent.com/php-soap/engine/main/src/Transport.php) : Replaces the old `HandlerInterface`

By default we will be using a [PSR-18](https://www.php-fig.org/psr/psr-18/) based transport.
The `ExtSoapClientTransport` is still available, but won't be used by default anymore,
because it has many known issues in PHP's bug tracker.

The downside is that you have to decide which PSR-18 HTTP client you want to use before installing this package.
For example:

```bash
composer require symfony/http-client nyholm/psr7
```

The HTTP middleware are now regular [httplug](http://httplug.io/) plugins, which are also moved to another package: 
You can find a full list of plugins that can be used here:

* [All HTTPlug plugins](https://docs.php-http.org/en/latest/plugins/)
* [Authentication plugins](https://github.com/php-soap/psr18-transport#authentication)
* [Soap - WSDL middleware](https://github.com/php-soap/psr18-transport/tree/main/src/Middleware)
* [WSSE - WSA middleware](https://github.com/php-soap/psr18-wsse-middleware)

### ExtSoap

https://github.com/php-soap/ext-soap-engine/

All `ext-soap` quirks are now maintained in a separate package.
This will most likely have impact on:

* [SoapOptions](https://github.com/php-soap/ext-soap-engine/#extsoapoptions): The same as before, but without metadata manipulators. You can now configure them in the Engine factory.
* [ClassMap](https://github.com/php-soap/ext-soap-engine/#classmap): It now takes variadic `ClassMap` arguments as arguments instead of an array.
* [TypeConverter](https://github.com/php-soap/ext-soap-engine/#typeconverter): Moved
* [WsdlProvider](https://github.com/php-soap/ext-soap-engine/#wsdlprovider): New WSDL providers were added to the new repo


If you have custom classes implementing one of the items above, they will require some love whilst upgrading.


**Note:**

* By default, the Transport does not contain any last request information anymore. There is a `TraceableTransport` available that you can decorate another transport with in order to get this functionality back.

## Dependency upgrades

* Symfony to LTS (4.4)
* PHP (^8.0)
* Removed a lot of old dependencies and suggestions
* Removed ext-*, since they are required in the specific php-soap packages

## Removed deprecations

### Events

The custom event dispatchers are removed.
We now support any [PSR-14 event dispatcher](https://www.php-fig.org/psr/psr-14/).

Fully qualified class names will be used as event names.
The old deprecated event names are now removed from the codebase.

The events won't contain the SOAP client anymore.
We don't want them to be service containers.
So instead, if you require the SOAP client in the event listeners, you need to inject them manually.

## Client

We don't work with a base client anymore.
Instead, a `Caller` is injected into the client you fully own.
The caller is responsible for transporting the request.
We provide an engine caller and an event dispatching caller
so that your client keeps on working how you expect it to.

Example of generated client:

```php
use Calculator\Type\Add;
use Calculator\Type\AddResponse;
use Phpro\SoapClient\Caller\Caller;

class CalculatorClient
{
    /**
     * @var Caller
     */
    private $caller;

    public function __construct(\Phpro\SoapClient\Caller\Caller $caller)
    {
        $this->caller = $caller;
    }

    public function add(Add $parameters): AddResponse
    {
        return ($this->caller)('Add', $parameters);
    }
}
```

We removed the debugging method from the soap-client.
Instead, you can either debug the request or result directly.
If you want to have access to the HTTP SOAP payload, we suggest adding a logger plugin to the HTTP client.
There is also a `TraceableTransport` available that can be used to detect the last SOAP request and response like you would in the old client.

This implies that changes where done to:

* the client generator
* the client factory generator
* the configuration generator

## Client Factory

In order to make the new `Caller` system available for your SOAP client,
the factory now injects the caller into your SOAP client.

```php
use Symfony\Component\EventDispatcher\EventDispatcher;
use Phpro\SoapClient\Soap\DefaultEngineFactory;
use Soap\ExtSoapEngine\ExtSoapOptions;
use Phpro\SoapClient\Caller\EventDispatchingCaller;
use Phpro\SoapClient\Caller\EngineCaller;

class CalculatorClientFactory
{
    public static function factory(string $wsdl): CalculatorClient
    {
        $engine = DefaultEngineFactory::create(
            ExtSoapOptions::defaults($wsdl, [])
                ->withClassMap(CalculatorClassmap::getCollection())
        );

        $eventDispatcher = new EventDispatcher();
        $caller = new EventDispatchingCaller(new EngineCaller($engine), $eventDispatcher);

        return new CalculatorClient($caller);
    }
}
```

You can opt-out on the event dispatching logic or decorate your own caller.

The `DefaultEngineFactory` can now be configured with a transport and the metadata options.
Full example on how you can personalize your factory class:

```php
use Http\Client\Common\PluginClient;
use Http\Discovery\Psr18ClientDiscovery;
use Phpro\SoapClient\Soap\DefaultEngineFactory;
use Phpro\SoapClient\Soap\Metadata\Manipulators\DuplicateTypes\RemoveDuplicateTypesStrategy;
use Phpro\SoapClient\Soap\Metadata\Manipulators\TypesManipulatorChain;
use Phpro\SoapClient\Soap\Metadata\MetadataOptions;
use Soap\ExtSoapEngine\ExtSoapOptions;
use Soap\ExtSoapEngine\Wsdl\Naming\Md5Strategy;
use Soap\ExtSoapEngine\Wsdl\PermanentWsdlLoaderProvider;
use Soap\Psr18Transport\Psr18Transport;
use Soap\Psr18Transport\Wsdl\Psr18Loader;

$httpClient = Psr18ClientDiscovery::find();
$engine = DefaultEngineFactory::create(
    ExtSoapOptions::defaults($wsdl, [])
        ->withClassMap(CalculatorClassmap::getCollection())
        ->withWsdlProvider(
            new PermanentWsdlLoaderProvider(
                Psr18Loader::createForClient($httpClient),
                new Md5Strategy(),
                'target/location'
            )
        ),
    Psr18Transport::createForClient(
        new PluginClient(
            $httpClient,
            [
                $middleware1,                        
                $middleware2,                        
            ]
        )
    ),
    MetadataOptions::empty()->withTypesManipulator(
        new TypesManipulatorChain(
            new RemoveDuplicateTypesStrategy()
        )
    )
);
```
