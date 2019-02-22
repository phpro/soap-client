# ExtSoapClientHandle

*Features: LastRequestInfoCollector*

```
❗️ Make sure ext-soap is loaded.
```

The ExtSoapServerHandle is used by default and works with the built-in `__doRequest()` method.
This Handle is not configurable and can be used for soap implementations which do not use extensions.
It is activated by default to get you going as quick as possible.


**Example usage**

```php
<?php

use Phpro\SoapClient\Soap\Driver\ExtSoap\ExtSoapEngineFactory;
use Phpro\SoapClient\Soap\Driver\ExtSoap\ExtSoapOptions;

$engine = ExtSoapEngineFactory::fromOptions(ExtSoapOptions::defaults($wsdl, []));
$client = new YourClient($engine, $eventDispatcher);
```
