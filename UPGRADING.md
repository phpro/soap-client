# V1 to V2

V1 has been around for quite some time.
We decided to move the [low-level SOAP core to a separate organisation](https://github.com/php-soap/).
This will make both this soap-client and the more low-level SOAP things easier to change.

The focus of this package is the generation of code so that you can easily interact with your SOAP service.
We decided to make it more opinionated, so that you have to deal with less strange SOAP related bugs than before.

By moving the low-level SOAP stuff out, we can now focus on improving metadata collection,
which will results in better generated classes.

We suggest to use the `soap-client `CLI tools again if you want to upgrade and existing application:

```
./vendor/bin/soap-client wizard
```

You can check the updated documentation in order to discover how you need to do specific actions like adding middleware in v2.


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

* https://github.com/php-soap/psr18-transport/

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

* The Transport does not contain any last request information anymore.

## Dependency upgrades

* Symfony to LTS (4.4)
* PHP (^8.0)
* Removed a lot of old dependencies and suggestions

## Removed deprecations

### Events

The custom event dispatchers are removed.
We now support any [PSR-14 event dispatcher](https://www.php-fig.org/psr/psr-14/).

Fully qualified class names will be used as event names.
The old deprecated event names are now removed from the codebase.

The events won't contain the soap client anymore.
We don't want them to be service containers.
So instead, if you require the soap client in the event listeners, you need to inject them manually.

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
If you want to have access to the HTTP soap payload, we suggest adding a logger plugin to the HTTP client.
There is also a `TraceableTransport` available that can be used to detect the last SOAP request and response.

This implies that changes where done to:

* the client generator
* the client factory generator
* the configuration generator

