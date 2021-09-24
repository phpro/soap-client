# Driver metadata

## Dealing with ext-soap issues

### Duplicate types

Ext-soap does not add any namespace or unique identifier to the types it knows.
You can read more about this in the [known ext-soap issues](../known-issues/ext-soap.md#duplicate-typenames) section.
Therefore, we added some strategies to deal with duplicate types:

**IntersectDuplicateTypesStrategy**

Enabled by default when using `DefaultEngineFactory::create()`.

This duplicate types strategy will merge all duplicate types into one big type which contains all properties.

**RemoveDuplicateTypesStrategy**

This duplicate types strategy will remove all duplicate types it finds.

You can overwrite the strategy on the `DefaultEngineFactory` object inside the client factory:

```php
<?php

use Phpro\SoapClient\Soap\DefaultEngineFactory;
use Phpro\SoapClient\Soap\ExtSoap\Metadata\Manipulators\DuplicateTypes\RemoveDuplicateTypesStrategy;
use Phpro\SoapClient\Soap\Metadata\MetadataOptions;

$engine = DefaultEngineFactory::create(
    $options, $transport,
    MetadataOptions::empty()->withTypesManipulator(
        new RemoveDuplicateTypesStrategy()
    )
);
```
