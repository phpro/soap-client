# General purpose SOAP-client

## Usage
```php

$wsdl = '';
$clientFactory = new ClientFactory(Client::class);
$soapOptions = [];

$clientBuilder = new ClientBuilder($clientFactory, $wsdl, $soapOptions);
$clientBuilder->withLogger(new Logger());
$clientBuilder->addClassMap(new ClassMap('WsdlType', PhpType::class));
$clientBuilder->addTypeConverter(new DateTimeTypeConverter());
$client = $clientBuilder->build();

```