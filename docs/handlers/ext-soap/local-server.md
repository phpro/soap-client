# ExtSoapServerHandle

*Features: LastRequestInfoCollector*

```
❗️ Make sure ext-soap is loaded.
```

The ExtSoapServerHandle can be used to link the soap-client to a local PHP SoapServer instance.
This handle can be used for testing purposes, it is not recommended to use it in production.

*NOTE: * Since SoapServer is sending headers, you want to run this handler in a separate process.
You can use `@runInSeparateProcess` in PHPunit.

**Example usage**

```php
<?php

use Phpro\SoapClient\Soap\Driver\ExtSoap\ExtSoapEngineFactory;
use Phpro\SoapClient\Soap\Driver\ExtSoap\ExtSoapOptions;
use Phpro\SoapClient\Soap\Driver\ExtSoap\Handler\ExtSoapServerHandle;

$soapServer = new \SoapServer($wsdl, []);
$soapServer->setObject($someTestingImplementation);

$engine = ExtSoapEngineFactory::fromOptionsWithHandler(
    ExtSoapOptions::defaults($wsdl, []),
    new ExtSoapServerHandle($soapServer)
);
$client = new YourClient($engine, $eventDispatcher);
```
