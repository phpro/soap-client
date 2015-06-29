# General purpose PHP SOAP-client

## Basic Usage
```php

$wsdl = 'http://path.to/your.wsdl';
$clientFactory = new ClientFactory(Client::class);
$soapOptions = [];

$clientBuilder = new ClientBuilder($clientFactory, $wsdl, $soapOptions);
$clientBuilder->withLogger(new Logger());
$clientBuilder->addClassMap(new ClassMap('WsdlType', PhpType::class));
$clientBuilder->addTypeConverter(new DateTimeTypeConverter());
$client = $clientBuilder->build();
```

## Generators

### Type Value-Objects
```sh
$ soap-client generate:types --wsdl=<wsdl> [--namespace=<namespace>] <destination>

Arguments:
  destination                  Destination folder

Options:
      --wsdl=WSDL              The WSDL on which you base the types
      --namespace=NAMESPACE    Resulting namespace
```

### ClassMaps
```sh
$ soap-client generate:classmap --wsdl=<wsdl> [--namespace=<namespace>]

Options:
      --wsdl=WSDL              The WSDL on which you base the types
      --namespace=NAMESPACE    Resulting namespace
```

## Creating your own SOAP Client
