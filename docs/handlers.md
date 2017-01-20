# Use your preferred data transfer layer with Handlers

The build-in SOAP client doesn't support extensions by default.
To overcome this problem, you are forced to overwrite the `__doRequest()` method.
By doing this, your code will be tied to the SoapClient which makes it hard to test and to reuse the logic.

This package contains a handler system which allows you to get control over the HTTP layer of the soap client.
The handler system makes it possible to make changes to the request and the response before sending it to the server. 

Here is a list of built-in handlers:

- [SoapHandle](#SoapHandle)
- [GuzzleHandle](#GuzzleHandle)


## SoapHandle

*Features: LastRequestInfoCollector*

The SoapHandle is used by default and works with the built-in `__doRequest()` method.
This Handle is not configurable and can be used for soap implementations which do not use extensions.
It is activated by default to get you going as quick as posile.

**Configuration**
```php
$clientBuilder = new ClientBuilder($clientFactory, $wsdl, $soapOptions);
$client = $clientBuilder->build();
```


## GuzzleHandle

*Features: LastRequestInfoCollector, MiddlewareSupporting*

With this handler it is easy to get in control about the HTTP layer of the SOAP client.
You can specify one or multiple middlewares that are being applied on your guzzle client.
This makes it possible to manipulate the request and response objects so that you can get full control.

This handler is based on middlewares which are applied to your guzzle client.
[You can read more about middlewares in this section.](middlewares.md)

**Dependencies**
```sh
composer require guzzlehttp/guzzle http-interop/http-factory-guzzle
```

**Configuration**
```php
$clientBuilder = new ClientBuilder($clientFactory, $wsdl, $soapOptions);
$clientBuilder->withHandler(GuzzleHandle::createForClient($guzzleClient));
$client = $clientBuilder->build();
```
