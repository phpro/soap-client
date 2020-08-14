# Basic Usage

Now that we explained all parts of your new SoapClient, it is time to interact with it.
 Look at following snippet:

```php
$client = MyclientFactory::factory($wsdl = 'http://path.to/your.wsdl');
$response = $client->helloWorld(new HelloWorldRequest('name'));
echo $response->getGreeting();
```

The client will be initialized inside your generated client factory.
 More information about the [generated client factory can be found here](cli/generate-clientfactory.md).

In the last part of the snippet you can see how the client works.
 It will use the generated value-objects to call the `RequestInterface` on the SoapClient.
 As a result the `ResultProviderInterface` will return the actual `ResultInterface` which contains the `getGreeting()` method.
 Readable, right?
 
A client implements a single WSDL, so when the service you are implementing has multiple WSDL's then you'll need to create a client for each of the WSDL's you want to use.
 You can then manually create a wrapper class if you desire to do so.

Next: [Test your SOAP client.](/docs/testing.md)
