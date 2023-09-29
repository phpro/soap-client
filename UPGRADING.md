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

    public function add(Add $parameters) : AddResponse
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
    public static function factory(string $wsdl) : CalculatorClient
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
use Phpro\SoapClient\Soap\ExtSoap\Metadata\Manipulators\DuplicateTypes\RemoveDuplicateTypesStrategy;use Phpro\SoapClient\Soap\Metadata\Manipulators\TypesManipulatorChain;
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
