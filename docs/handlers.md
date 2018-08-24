# Use your preferred data transfer layer with Handlers

The build-in SOAP client doesn't support extensions by default.
To overcome this problem, you are forced to overwrite the `__doRequest()` method.
By doing this, your code will be tied to the SoapClient which makes it hard to test and to reuse the logic.

This package contains a handler system which allows you to get control over the HTTP layer of the soap client.
The handler system makes it possible to make changes to the request and the response before sending it to the server. 

Here is a list of built-in handlers:

- [SoapHandle](#soaphandle)
- [HttPlugHandle](#guzzlehandle)
- [LocalSoapServerHandle](#localsoapserverhandle)


## SoapHandle

*Features: LastRequestInfoCollector*

The SoapHandle is used by default and works with the built-in `__doRequest()` method.
This Handle is not configurable and can be used for soap implementations which do not use extensions.
It is activated by default to get you going as quick as posible.

**Configuration**
```php
$clientBuilder = new ClientBuilder($clientFactory, $wsdl, $soapOptions);
$client = $clientBuilder->build();
```


## HttPlugHandle

*Features: LastRequestInfoCollector, MiddlewareSupporting*

[HTTPlug](http://httplug.io/) is a HTTP client abstraction that can be used with multiple client packages.
With this handler it is easy to get in control about the HTTP layer of the SOAP client.
You can specify one or multiple middlewares that are being applied on your http client.
This makes it possible to manipulate the request and response objects so that you can get full control.

This handler is based on middlewares which are applied to your guzzle client.
[You can read more about middlewares in this section.](middlewares.md)

**Dependencies**

Load HTTP plug core packages:

```sh
composer require psr/http-message:^1.0 php-http/httplug:^1.1 php-http/message-factory:^1.0 php-http/discovery:^1.3 php-http/message:^1.6 php-http/client-common:^1.6
```

**Select HTTP Client**

Select one of the many clients you want to use to perform the HTTP requests:
http://docs.php-http.org/en/latest/clients.html#clients-adapters

```sh
composer require php-http/client-implementation:^1.0
```

**Configuration**
```php
$clientBuilder = new ClientBuilder($clientFactory, $wsdl, $soapOptions);
$clientBuilder->withHandler(HttPlugHandle::createForClient($httpClient));
$client = $clientBuilder->build();
```

## LocalSoapServerHandle

*Features: LastRequestInfoCollector*

The LocalSoapServerHandle can be used to link the soap-client to a local PHP SoapServer instance.
This handle can be used for testing purpose, it is not recommended to use it in production.

*NOTE: * Since SoapServer is sending headers, you want to run this handler in a separate process.
You can use `@runInSeparateProcess` in PHPunit.


**Configuration**
```php
$soapServer = new \SoapServer('some.wsdl', []);
$soapServer->setObject($someTestingImplementation);

$clientBuilder = new ClientBuilder($clientFactory, $wsdl, $soapOptions);
$clientBuilder->withHandler(new LocalSoapServerHandle($soapServer));
$client = $clientBuilder->build();
```
