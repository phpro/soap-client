# HttPlugHandle

*Features: LastRequestInfoCollector, MiddlewareSupporting*

[HTTPlug](http://httplug.io/) is a HTTP client abstraction that can be used with multiple client packages.
With this handler it is easy to get in control about the HTTP layer of the SOAP client.
You can specify one or multiple middlewares that are being applied on your http client.
This makes it possible to manipulate the request and response objects so that you can get full control.

This handler knows how to deal with HTTP middlewares if they are supported by your HTTP client.
[You can read more about middlewares in this section.](../middlewares.md)

**Dependencies**

Load HTTP plug core packages:

```sh
composer require psr/http-message:^1.0 php-http/httplug:^2.1 php-http/message-factory:^1.0 php-http/discovery:^1.7 php-http/message:^1.8 php-http/client-common:^2.1
```


**Select HTTP Client**

Select one of the many clients you want to use to perform the HTTP requests:
http://docs.php-http.org/en/latest/clients.html#clients-adapters

```sh
composer require php-http/client-implementation:^1.0
```

**Example usage**

```php
<?php

use Http\Adapter\Guzzle6\Client;
use Phpro\SoapClient\Middleware\BasicAuthMiddleware;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Phpro\SoapClient\Soap\Driver\ExtSoap\ExtSoapEngineFactory;
use Phpro\SoapClient\Soap\Driver\ExtSoap\ExtSoapOptions;
use Phpro\SoapClient\Soap\Handler\HttPlugHandle;

$handler = HttPlugHandle::createForClient(
   Client::createWithConfig(['headers' => ['User-Agent' => 'testing/1.0']])
);
$handler->addMiddleware(new BasicAuthMiddleware('user', 'password'));

$engine = ExtSoapEngineFactory::fromOptionsWithHandler(
    ExtSoapOptions::defaults($wsdl, [])
        ->withClassMap(YourClassmap::getCollection()),
    $handler
);
$eventDispatcher = new EventDispatcher();
$client = new YourClient($engine, $eventDispatcher);
```
