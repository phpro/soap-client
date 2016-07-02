[![Build status](https://api.travis-ci.org/phpro/soap-client.svg)](http://travis-ci.org/phpro/soap-client)
[![Installs](https://img.shields.io/packagist/dt/phpro/soap-client.svg)](https://packagist.org/packages/phpro/soap-client/stats)
[![Packagist](https://img.shields.io/packagist/v/phpro/soap-client.svg)](https://packagist.org/packages/phpro/soap-client)

# General purpose PHP SOAP-client

Sick and tired of building crappy SOAP implementations?
 This package aims to help you with some common SOAP integration pains in PHP.
 It's goal is to make integrating with SOAP fun again!
 Let's inspect some of the pains that are solved by this package:
 
By default the SoapClient works with a mix of arrays, stdClasses and other scalar types. 
 This is not a good practice in a modern OOP world because:
 
- It makes your code hard to test.
- It breaks Code Completion
- It is hard to track changes in the response and request objects.
- The content of a result is never explicit.
 
To solve the above problems, this package will force you into using Value-objects for Requests and Responses.
 We know that maintaining these value-objects can be a load of work. 
 No worries! There are some commandline tools available that will help you with generating a good base to start with.
 Because the SoapClient will need a classmap of WSDL to PHP Classes, there is also a classmap generator available.
 This will surely safe you a lot of time!
 By adding SOAP type converters, it is possible to transform the values of a specific SOAP type from/to a PHP type.
 The package comes included with some basic transformers for date and datetime.
 On, top of that, it is very easy to create your own transformers.
 
Another problem is that the native SoapClient works with a lot of magic methods.
 It is hard to debug these magic methods hence there is no code completion.
 Both SOAP and implementation methods are on the same object.
 
This package will force you into wrapping a SoapClient into your own Client.
 You can choose to only expose the methods you need. 
 It will always be clear to the developer what's in your client, how it works and what it returns.
 We even provided a base Client for you to use with some common used methods for debugging, authentication and an event system.
 
In lots of SOAP integrations the logging, caching and Soap calls are in the same method.
 This makes your code hard to read and dependent on other classes / packages.

It is important keep your code clean. This is why we added an event-listener to your Soap client.
 You can hook in at every important step of the SOAP flow.
 This way it is possible to add logging, caching and error handling with event subscribers. 
 Pretty cool right?!
 
Testing webservices is hard! 
 That is Why this package is fully compatible with [php-vcr](http://php-vcr.github.io/).
 Testing your SOAP client will be very fast and without any errors at the 3th party side of the integration. 
 
Last but not least, we want to make it easy for you to configure your SoapClient.
 That is why we included a ClientBuilder on which you can configure your custom Client.
 You want some other settings during development and in production? 
 No problem! Sit back and let the ClientBuilder handle your Client initialisation.
 
Great, you made it so far! Let's find out how this module works:
 
## Installation

```sh
$ composer require phpro/soap-client
```

## Creating your custom client

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

 
## Generating Value-objects

Basic value-objects can be generated automatically.

```sh
$ soap-client generate:types --wsdl=<wsdl> [--namespace=<namespace>] <destination>

Arguments:
  destination                  Destination folder

Options:
      --wsdl=WSDL              The WSDL on which you base the types
      --namespace=NAMESPACE    Resulting namespace
```

This generator will read all XSD types from the provided WSDL and convert it to PHP classes.
 You can specify a namespace and a location where the classes will be stored.
 The properties from the XSD will be added as private properties to the value-objects.
 When the classes already exist, a patch operation is performed and a backup file is created. 
 This way your custom code will always remain available.

Keep in mind that the WSDL must provide all XSD types for the generation of value-objects.
 Some exotic SOAP services don't provide much information. For example: they will return an XML string which needs to be parsed manually.
 These WSDLs can only be parsed as far as the XSD information goes.
 Al other information needs to be added manually, or by a custom class generator.

When the value objects are generated, you will still need to customize them.
 For example by adding the required interfaces:

```php
class HelloWorldRequest implements RequestInterface
{
    public function __construct($name) {
        $this->name = $name
    }

    // Generated code
}

class HelloWorldResponse implements ResponseProviderInterface
{
    // Generated code
    
    public function getResponse()
    {
        return $this->greeting;
    }
}

class Greeting implements ResponseInterface
{
    // Generated code
    
    public function getGreeting()
    {
        return $this->greeting;
    }
}

``` 

## Generating class maps

When the value-objects are generated, we need to tell SOAP about how the PHP classes are mapped to the XSD types.
 This is done by a class map, which can be a really boring manual task.
 Luckily a class map generator is added, which you can use to parse the classmap from the WSDL.

```sh
$ soap-client generate:classmap --wsdl=<wsdl> [--namespace=<namespace>]

Options:
      --wsdl=WSDL              The WSDL on which you base the types
      --namespace=NAMESPACE    Resulting namespace
```

This command will generate a class map and display it on the screen.
 You will just need to copy it and next paste it in the `ClientBuilder`.
 
Example output:

```php
return new ClassMapCollection(
    [
        new ClassMap('HelloWorldRequest', \HelloWorldRequest::class),
        new ClassMap('HelloWorldResponse', \HelloWorldResponse::class),
        new ClassMap('Greeting', \Greeting::class),
    ]
);
```

## Convert SOAP types

Some exotic XSD types are hard to transform to PHP objects.
 A typical example are dates: some people like it as a timestamp, some want it as a DateTime, ...
 By adding custom TypeConverters, it is possible to convert a WSDL type to / from a PHP type.
 
These TypeConverters are added by default:

- DateTimeTypeConverter 
- DateTypeConverter

You can also create your own converter by implementing the `TypeConverterInterface`. For example:

```php
class MyCustomConverter implements TypeConverterInterface
{
    // Implement methods...
}
```
 
## Basic Usage

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

## Hooking in with events

The `Client` class has a build-in EventDispatcher.
 It will trigger events at all important phases of the SOAP call: 

- Events::REQUEST (RequestEvent)
- Events::RESPONSE (ResponseEvent)
- Events::FAULT (FaultEvent)

You can subscribe your own listeners to the configured `EventDispatcher`. For example:

```php
class ResponseFailedSubscriber implements SubscriberInterface
{
    // implement interface
}

$dispatcher->addSubscriber(new ResponseFailedSubscriber());
```

This package ships with some default subscriber plugins:

### Logger plugin

The logger plugin is activated automatically when you attach a `LoggerInterface` to the `ClientBuilder`.
 It will hook in to the Request, Response and Fault event and will log every step of the SOAP process.
 No more code pollution for logging!


### Caching plugin

This repository does not contain a caching plugin.
 If you want to be able to cache the methods in your SOAP client, you could use our 
 [annotated-cache](https://github.com/phpro/annotated-cache) package.
 
 This package will make it possible to specify the caching configuration in the annotations of your SOAP client.
 
```php

use Phpro\AnnotatedCache\Annotation\Cacheable;

class YourClient extends Client
{
    /**
     * @Cacheable(pools="soapclient-pool", key="request", tags="helloworld", ttl=500)
     */
    public function helloWorld(RequestInterface $request)
    {
        return $this->call('HelloWorld', $request);
    }
}
```

## Testing

As mentioned earlier, it is very easy to integrate this project with [php-vcr](http://php-vcr.github.io/).
 This makes it possible to created fixtures of all your SOAP calls.
 By loading the fixtures, no actual calls will be done to the SOAP endpoint.
 This will make your tests fast, deterministic and accurate!
 Her is an example of a PHPUnit test:
 
```php
/**
 * @test
 * @vcr my-fixture-file.yml
 *
 */
function it_should_greet()
{
    $response = $this->client->helloWorld(new HelloWorldRequest('name'));
    $this->assertEquals('Hello name', $response->getGreeting());
}
```

The first time you run this test, a fixtures file `my-fixture-file.yml` will be created.
 The second time, this file will be used instead of running actual requests.
 Test it out, you will love it!
 
