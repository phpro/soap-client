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
     * @throws \SoapFault
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
