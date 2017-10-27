# Basic Usage

Now that we explained all parts of your new SoapClient, it is time to interact with it.
 Take a look at following snippet:

```php
$wsdl = 'http://path.to/your.wsdl';
$clientFactory = new ClientFactory(YourClient::class);
$soapOptions = [
    'cache_wsdl' => WSDL_CACHE_NONE
];

$clientBuilder = new ClientBuilder($clientFactory, $wsdl, $soapOptions);
$clientBuilder->withLogger(new Logger());
$clientBuilder->withEventDispatcher(new EventDispatcher());
$clientBuilder->addClassMap(new ClassMap('WsdlType', PhpType::class));
$clientBuilder->addTypeConverter(new DateTimeTypeConverter());
$client = $clientBuilder->build();

$response = $client->helloWorld(new HelloWorldRequest('name'));
echo $response->getGreeting();
```

<sub>Note: The `Logger` class is not provided by this package, use any PSR-3 compatible logger here (i.e. monolog).</sub>

In the first part of the snippet you can see the global configuration of your own SOAP client.
 The `YourClient` class will be instantiated by a `ClientFactory`, which is responsible for injecting the client dependencies.
 It is possible to use the same Client with different WSDL endpoints and SOAP options.
 This makes it easy for changing between environments.
 
Next, the client will be configured by the `ClientBuilder`.
 As you can see it is possible to add a Logger, EventDispatcher, Classmaps and TypeConverters.
 This makes the Soap client fully configurable.

In the last part of the snippet you can see how the client works.
 It will use the generated value-objects to call the `RequestInterface` on the SoapClient.
 As a result the `ResultProviderInterface` will return the actual `ResultInterface` which contains the `getGreeting()` method.
 Pretty readable right?
 
A client implements a single WSDL, so when the service you are implementing has multiple WSDL's then you'll need to create a client for each of the WSDL's you want to use.
 You can then manually create a wrapper class if you desire to do so.