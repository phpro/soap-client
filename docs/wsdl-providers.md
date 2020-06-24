# Get in control of the WSDL file

The built-in SOAP client does not give you control on how the WSDL is downloaded to the system.
 Therefor we've created a `WsdlProvider` mechanism that can be customized to your demands.


**Example usage**

```php
/** @var \Phpro\SoapClient\Wsdl\Provider\WsdlProviderInterface $provider */
$wsdl = $provider->provide('my.wsdl');
```


**Example usage with SoapOptions**
```php
<?php
use Phpro\SoapClient\Soap\Driver\ExtSoap\ExtSoapOptions;

/** @var \Phpro\SoapClient\Wsdl\Provider\WsdlProviderInterface $provider */
$options = ExtSoapOptions::defaults('my.wsdl')
    ->withWsdlProvider($provider);
```


Here is a list of built-in providers:

- [CachedWsdlProvider](#cachedwsdlprovider)
- [~~HttPlugWsdlProvider~~](#httplugwsdlprovider)
- [InMemoryWsdlProvider](#inmemorywsdlprovider)
- [LocalWsdlProvider](#localwsdlprovider)
- [MixedWsdlProvider](#mixedwsdlprovider)

Can't find the wsdl provider you were looking for?
[It is always possible to create your own one!](#creating-your-own-wsdl-provider)

## CachedWsdlProvider

This provider can permanently or temporary cache a (remote) WSDL.
This one is very useful to use in production, where the WSDL shouldn't change too much.
You can force it to load to a permanent location in e.g. a cronjob.
It will improve performance since the soap-client won't have to fetch the WSDL remotely.

**Dependencies**

If you want to use Httplug for loading the WSDL, [you need to follow these installation guidelines](./handlers/httplug.md).

**Usage**

```php
<?php
use Http\Client\Common\PluginClient;
use Http\Discovery\Psr17FactoryDiscovery;
use Phpro\SoapClient\Wsdl\Loader\HttpWsdlLoader;
use Phpro\SoapClient\Wsdl\Provider\CachedWsdlProvider;
use Symfony\Component\Filesystem\Filesystem;

$loader = new HttpWsdlLoader(
    new PluginClient($httpClient, [
        // You can add WSDL middlewares in here. E.g.: authentication, manipulations, ...
    ]),
    Psr17FactoryDiscovery::findRequestFactory()
);
$provider = new CachedWsdlProvider($loader, new Filesystem(), sys_get_temp_dir());
$wsdl = $provider->provide('https://somehost/service.wsdl');
```

**Download permanent cache**

```php
$provider->forcePermanentDownloads()->provide('http://somehost/service.wsdl');
```

This will store the WSDL in a permanent location.
From now on, the provider will always load the permanent file.
Unless you re-enable the force permanent method or remove the cache manually.
This is typically something you would want to create a CLI command or a cronjob for. 


**Note**

Only the main WSDL file is being downloaded at the moment. Ext-soap imports additional files internally.


## ~~HttPlugWsdlProvider~~

*Deprecated* : Will be removed in v2.0 - Use the `CachedWsdlProvider` in combination with the `HttpWsdlLoader` instead.

[HTTPlug](http://httplug.io/) is a HTTP client abstraction that can be used with multiple client packages.
The HTTPlug WSDL provider can be used for downloading remote WSDLs.
It has support for [middlewares](middlewares.md) so that you have full control over the HTTP request and response.
This way, you will always be able to download and manipulate the WSDL file even if it secured with e.g. NTLM.

**Dependencies**

If you want to use Httplug for loading the WSDL, [you need to follow these installation guidelines](./handlers/httplug.md).

**Usage**
```php
<?php
use Phpro\SoapClient\Wsdl\Provider\HttPlugWsdlProvider;

$provider = HttPlugWsdlProvider::createForClient($client);

// Optional location:
$provider->setLocation('/some/destination/file.wsdl');

// Middlewares support:
$provider->addMiddleware($middleware);
```

*Note:* If you want to cache the WSDL so that you don't have to download it on every request, you can use ext-soap `cache_wsdl` option.

To change the TTL of the cache, you can adjust following `php.ini` setting:

```php
# See: http://php.net/manual/en/soap.configuration.php
soap.wsdl_cache_ttl: 86400
```


## InMemoryWsdlProvider

By using the in-memory WSDL provider, you can just use a complete XML version of the WSDL as source.
This one might come in handy during tests, but probably shouldn't be used in production.

**Usage**
```php
<?php
use Phpro\SoapClient\Wsdl\Provider\InMemoryWsdlProvider;

$provider = new InMemoryWsdlProvider();
$wsdl = $provider->provide('<definitions ..... />');
```


## LocalWsdlProvider

The local WSDL provider can be used to load a local file.
It contains an additional check to make sure that the WSDL file exists and throws a `WsdlException` if it does not exist.

**Usage**
```php
<?php
use Phpro\SoapClient\Wsdl\Provider\LocalWsdlProvider;

$provider = LocalWsdlProvider::create();
```


## MixedWsdlProvider

The mixed WSDL provider is used by default. 
You can pass every string you would normally pass to the built-in SOAP client's wsdl option.
No additional checks are executed, the loading of the file will be handled by the internal `SoapClient` class.

**Usage**
```php
<?php
use Phpro\SoapClient\Wsdl\Provider\MixedWsdlProvider;

$provider = new MixedWsdlProvider();
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
