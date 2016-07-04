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


## My SOAP service does not work with Request / Response objects.

In older SOAP services, it is possible that it is impossible to request with a `RequestInterface`.
 Those services typically require multiple SOAP arguments in the method.
 This is why we created a `MixedArgumentRequestInterface`.
 With this interface, you can still use our SOAP client, but send multiple arguments to the SOAP service.

```php
$request = new MultiArgumentRequest(['argument1', 'argument2'])
$response = $client->someMethodWithMultipleArguments($request)
```

When the SOAP service is returning an internal PHP type, the result is being wrapped with a `MixedResult` class.
  This way, you don't have to worry about the internal type of the SOAP response.
  
```php
/** @var MixedResult $result */
$result = $client->someMethodWithInternalTypeResult($request);
$actualResponse = $response->getResponse();
```
