# Creating your custom client

The first thing you need to do is creating your own client.
 We provided a base Client class which already has some basic features.
 An example of your custom Client looks like this:

```php
class YourClient extends Client
{
    /**
     * @param RequestInterface $request
     *
     * @return ResultInterface
     * @throws \Phpro\SoapClient\Exception\SoapException
     */
    public function helloWorld(RequestInterface $request)
    {
        return $this->call('HelloWorld', $request);
    }
}
```

As you can see, this custom client extends the `Client` class.
 It is also possible to implement the `ClientInterface`.
 This means you will also have to create a ClientFactory which can instantiate your custom Client.
 The SoapClient is injected inside your `YourClient` class and will not be accessible to the outside world.
 
The methods of the class are explicitly defined and have explicit parameters and return types.
 Request value-objects that are passed to the `call` method, MUST implement the `RequestInterface`.
 SOAP responses can have 2 types: `ResultProviderInterface` or `ResultInterface`.
 The `ResultProviderInterface` can be used if the response type is wrapping a `ResultInterface`.
 The `call` method will initailize the SOAP call and trigger the subscribed event listeners.

As you can see, we've normalized the exception. Our SOAP client will always throw a custom `SoapException`.
 This is to make it possible to use multiple different handlers which don't always throw a `SoapFault`.


## My SOAP service does not work with Request / Response objects.

In older SOAP services, it is possible that it is impossible to request with a `RequestInterface`.
 Those services typically require multiple SOAP arguments in the method.
 This is why we created a `MultiArgumentRequestInterface`.
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

Next: [Generate PHP classes based on SOAP types.](cli/generate-types.md)
