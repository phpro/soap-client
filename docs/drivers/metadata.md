# Driver metadata

The metadata part of the driver knows what objects and functions are inside the soap service.
This can be done by parsing the WSDL file.
Every driver has its own way of collecting this information.
On top of that, we provide some generic tools that can be useful.

# Built-in metadata tools

- [LazyInMemoryMetadata](#lazyinmemorymetadata)
- [ManipulatedMetadata](#manipulatedmetadata)

## LazyInMemoryMetadata

This class will make sure that the metadata are only collected once and stores it in memory for a second access.

Example output:

```php
<?php

use \Phpro\SoapClient\Soap\Engine\Metadata\LazyInMemoryMetadata;
use \Phpro\SoapClient\Soap\Engine\Metadata\MetadataFactory;

$lazy = MetadataFactory::lazy($actualMetadataInterface);
```

## ManipulatedMetadata

This class makes it possible to manipulate metadata on the-fly.
This can be handy for changing things before code is generated.
Example use-cases are the duplicate type strategies in the ExtSoap driver.

Example output:

```php
<?php

use \Phpro\SoapClient\Soap\Engine\Metadata\ManipulatedMetadata;
use Phpro\SoapClient\Soap\Engine\Metadata\Manipulators\MethodsManipulatorChain;
use Phpro\SoapClient\Soap\Engine\Metadata\Manipulators\TypesManipulatorChain;
use \Phpro\SoapClient\Soap\Engine\Metadata\MetadataFactory;
use Phpro\SoapClient\Soap\Engine\Metadata\MetadataOptions;

$lazyManipulated = MetadataFactory::manipulated(
    $actualMetadataInterface,
    (new MetadataOptions())
        ->withMethodsManipulator(
            new MethodsManipulatorChain()
        )
        ->withTypesManipulator(
            new TypesManipulatorChain()
        )
);
```
