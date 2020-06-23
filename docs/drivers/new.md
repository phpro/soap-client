# Creating your own SOAP driver

It is possible to use whatever package you want to handle metadata parsing, encoding and decoding inside the soap client.
This is done by specifying a custom driver. A driver consist of an encoder, a decoder and a metadata provider.

- [Drivers](#drivers)
- [Encoders](#encoders)
- [Decoders](#decoders)
- [MetadataProviders](#metadataproviders)
- [Composition](#composition)


## Drivers

You can create your own SOAP by implementing the `Phpro\SoapClient\Soap\Engine\DriverInterface`

```php
<?php

use Phpro\SoapClient\Soap\Engine\DriverInterface;
use Phpro\SoapClient\Soap\Engine\Metadata\MetadataInterface;
use Phpro\SoapClient\Soap\HttpBinding\SoapRequest;
use Phpro\SoapClient\Soap\HttpBinding\SoapResponse;

class MyDriver implements DriverInterface
{
    // Encode a soap request into an XML string
    public function encode(string $method, array $arguments) : SoapRequest;
    
    // Decode a soap request into actual objects
    public function decode(string $method, SoapResponse $response);
    
    // Parse metadata from the WSDL
    public function getMetadata() : MetadataInterface;
}
```

To make sure that your driver works, you have to create a new test case for your implementation.
We've provided the `PhproTest\SoapClient\Integration\Soap\Engine\AbstractIntegrationTest` to make sure all kinds of encodings are covered.


## Encoders

Encoders are responsible for transforming the `Client::call()` method arguments into an actual SOAP request. 
They implement the `Phpro\SoapClient\Soap\Engine\EncoderInterface`.

You can create your own encoder by implementing this interface:

```php
<?php

use Phpro\SoapClient\Soap\Engine\EncoderInterface;
use Phpro\SoapClient\Soap\HttpBinding\SoapRequest;

class MyEncoder implements EncoderInterface
{   
    // Encode a soap request into an XML string
    public function encode(string $method, array $arguments) : SoapRequest;
}
```

Since encoding is a rather complex topic, you need to make sure it works the way we expect it to work.
Therefor, you need to create a testcase for your implementation.
We've provided the `PhproTest\SoapClient\Integration\Soap\Engine\AbstractEncoderTest` to make sure all kinds of encodings are covered.


## Decoders

Decoders are responsible for transforming the SOAP response back into PHP objects. 
They implement the `Phpro\SoapClient\Soap\Engine\DecoderInterface`.

You can create your own decoder by implementing this interface:

```php
<?php

use Phpro\SoapClient\Soap\Engine\DecoderInterface;
use Phpro\SoapClient\Soap\HttpBinding\SoapResponse;

class MyDecoder implements DecoderInterface
{
    public function decode(string $method, SoapResponse $response);
}
```

Since decoding is a rather complex topic, you need to make sure it works the way we expect it to work.
Therefor, you need to create a testcase for your implementation.
We've provided the `PhproTest\SoapClient\Integration\Soap\Engine\AbstractDecoderTest` to make sure all kinds of encodings are covered.


## MetadataProviders

The metadata part of the driver knows what objects and functions are inside the soap service. This can be done by parsing the WSDL file.
You can create custom metadata implementations by creating a new `Phpro\SoapClient\Soap\Engine\Metadata\MetadataProviderInterface` implementation.

```php
<?php

use Phpro\SoapClient\Soap\Engine\Metadata\MetadataProviderInterface;
use Phpro\SoapClient\Soap\Engine\Metadata\MetadataInterface;

class MyMetadataProvider implements MetadataProviderInterface
{
    // Parse metadata from the WSDL
    public function getMetadata() : MetadataInterface;
}
```

We provided some generic metadata tools that you can use to e.g. manipulate the metadata during code generation.
You can find [more information about the metadata on this page](./metadata.md).

Since detecting the metdata is a rather complex topic, you need to make sure it works the way we expect it to work.
Therefor, you need to create a testcase for your implementation.
We've provided the `PhproTest\SoapClient\Integration\Soap\Engine\AbstractMetadataProviderTest` to make sure all kinds of types are covered.


## Composition

The `DriverInterface` is a composition of following interfaces:

- `EncoderInterface`
- `DecoderInterface`
- `MetadataProviderInterface`

This means that you can compose a new driver out of existing interface.
You can for example only provide a new `MetadataProviderInterface`, but still use built-in encoders and decoders.
