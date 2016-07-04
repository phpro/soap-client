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

Creating a great OO SOAP client means that you'll have to create a lot of code.
 This can be a tedious task which can be automated. 
 That is why we've added the tools to automatically generate the SOAP objects from the XSD scheme inside the WSDL.
 It is even possible to specify your own code-generation rules and code assemblers or use one of our many built-in classes.

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

## Getting your SOAP integration up and running

1. [Create your own SOAP client.](docs/client.md)
2. [Generate PHP classes based on SOAP types.](docs/cli/generate-types.md)
3. [Generate a class map](docs/cli/generate-classmap.md)
4. [Add type converters](docs/type-converter.md)
5. [Listen to events](docs/events.md)
  - [Logger plugin](docs/plugins/logger.md)
  - [Caching plugin](docs/plugins/caching.md)
6. [Use your SOAP client.](docs/usage.md)
7. [Test your SOAP client.](docs/testing.md)


## Customize the code generators

- [Configuration](docs/code-generation/configuration.md)
- [Specify generation `Rules`](docs/code-generation/rules.md)
- [Generate code through `Assemblers`](docs/code-generation/assemblers.md)

