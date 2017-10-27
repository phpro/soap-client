# Get in control of the WSDL file

The built-in SOAP client does not give you control on how the WSDL is downloaded to the system.
 Therefor we've created a `WsdlProvider` mechanism that can be customized to your demands.


General configuration:

```php
$clientBuilder = new ClientBuilder($clientFactory, $wsdl, $soapOptions);
$clientBuilder->withWsdlProvider($provider);
$client = $clientBuilder->build();
```

Here is a list of built-in providers:

- [HttPlugWsdlProvider](#httplugwsdlprovider)
- [LocalWsdlProvider](#localwsdlprovider)
- [MixedWsdlProvider](#mixedwsdlprovider)

Can't find the wsdl provider you were looking for?
[It is always possible to create your own one!](#creating-your-own-wsdl-provider)

## HttPlugWsdlProvider

[HTTPlug](http://httplug.io/) is a HTTP client abstraction that can be used with multiple client packages.
The HTTPlug WSDL provider can be used for downloading remote WSDLs.
It has support for [middlewares](middlewares.md) so that you have full control over the HTTP request and response.
This way, you will always be able to download and manipulate the WSDL file even if it secured with e.g. NTLM.

**Dependencies**

Load HTTP plug core packages:

```sh
composer require php-http/httplug:^1.0 php-http/message-factory:^1.0 php-http/discovery:^1.0 php-http/message:^1.0
```

**Select HTTP Client**

Select one of the many clients you want to use to perform the HTTP requests:
http://docs.php-http.org/en/latest/clients.html#clients-adapters

```sh
composer require php-http/client-implementation:^1.0
```

**Configuration**
```php
$provider = HttPlugWsdlProvider::createForClient($client);

// Optional location:
$provider->setLocation('/some/destination/file.wsdl');

// Middlewares support:
$provider->addMiddleware($middleware);
```

*Note:* If you want to cache the WSDL so that you don't have to download it on every request, you can use the built-in caching options:

```php
$clientBuilder = new ClientBuilder($clientFactory, $wsdl, [
    'cache_wsdl' => WSDL_CACHE_BOTH,
]);
```

To change the TTL of the cache, you can adjust following `php.ini` setting:

```php
# See: http://php.net/manual/en/soap.configuration.php
soap.wsdl_cache_ttl: 86400
```


## LocalWsdlProvider

The local WSDL provider can be used to load a local file.
It contains an additional check to make sure that the WSDL file exists and throws a `WsdlException` if it does not exist.

**Configuration**
```php
$clientBuilder = new ClientBuilder($clientFactory, $wsdl, $soapOptions);
$clientBuilder->withWsdlProvider(LocalWsdlProvider::create());
$client = $clientBuilder->build();
```


## MixedWsdlProvider

The mixed WSDL provider is used by default. 
You can pass every string you would normally pass to the built-in SOAP client's wsdl option.
No additional checks are executed, the loading of the file will be handled by the internal `SoapClient` class.

**Configuration**
```php
$clientBuilder = new ClientBuilder($clientFactory, $wsdl, $soapOptions);
$clientBuilder->withWsdlProvider(new MixedWsdlProvider());
$client = $clientBuilder->build();
```


## Creating your own WSDL provider

Didn't find the WSDL provider you needed? No worries! It is very easy to create your own WSDL provider.
The only thing you'll need to do is implement the WsdlProviderInterface:


```php
class MyWsdlProvider extends \Phpro\SoapClient\Wsdl\Provider\WsdlProviderInterface
{
    /**
     * The result of this method should be the link to the WSDL that can be used by the PHP soap-client.
     *
     * {@inheritdoc}
     */
    public function provide(string $source)
    {
        return $source;
    }
}
```
