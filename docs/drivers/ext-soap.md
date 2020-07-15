# ExtSoapDriver

```
❗️ Make sure ext-soap is loaded.
```

This soap driver wraps PHPs ext-soap `\SoapClient` implementation.

- It abuses the `__doRequest()` method to make it possible to encode the request and decode the response.
- Metadata is being parsed based on the `__getTypes()` and `__getFunctions()` method.

**Example usage**

```php
<?php

use Phpro\SoapClient\Soap\Driver\ExtSoap\ExtSoapEngineFactory;
use Phpro\SoapClient\Soap\Driver\ExtSoap\ExtSoapOptions;

$engine = ExtSoapEngineFactory::fromOptions(ExtSoapOptions::defaults($wsdl, []));
$client = new MyClient($engine, $eventDispatcher);
````

## ExtSoapOptions

This package provides a little wrapper around all available `\SoapClient` options.
We provide some default options and the additional options can be configured in a sane way.
It will validate the options before they are passed to the `\SoapClient`.
This way, you'll spend less time browsing the official PHP documentation.

**Example usage**

```php
<?php

use Phpro\SoapClient\Soap\Driver\ExtSoap\ExtSoapOptions;
use Phpro\SoapClient\Wsdl\Provider\HttPlugWsdlProvider;

$options = ExtSoapOptions::defaults($wsdl, ['location' => 'http://somedifferentserver.com'])
    ->disableWsdlCache()
    ->withClassMap(MyClassMap::getCollection())
    ->withWsdlProvider(HttPlugWsdlProvider::createForClient($httpClient));

$typemap = $options->getTypeMap();
$typemap->add(new MyTypeConverter());
```

## Dealing with ext-soap issues

### Duplicate types

Ext-soap does not add any namespace or unique identifier to the types it knows.
You can read more about this in the [known ext-soap issues](../known-issues/ext-soap.md#duplicate-typenames) section.
Therefore, we added some strategies to deal with duplicate types:

**IntersectDuplicateTypesStrategy**

Enabled by default when using `ExtSoapOptions::defaults()`.

This duplicate types strategy will merge all duplicate types into one big type which contains all properties.

**RemoveDuplicateTypesStrategy**

This duplicate types strategy will remove all duplicate types it finds.



You can overwrite the strategy on the `ExtSoapOptions` object:

```php
<?php

use Phpro\SoapClient\Soap\Driver\ExtSoap\ExtSoapOptions;
use Phpro\SoapClient\Soap\Driver\ExtSoap\Metadata\Manipulators\DuplicateTypes\RemoveDuplicateTypesStrategy;
use Phpro\SoapClient\Soap\Engine\Metadata\MetadataOptions;

$options = ExtSoapOptions::defaults($wsdl)
    ->withMetadataOptions(function (MetadataOptions $options): MetadataOptions {
        return $options->withTypesManipulator(
            new RemoveDuplicateTypesStrategy()
        );
    });
```
