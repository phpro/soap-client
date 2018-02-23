[![Build status](https://api.travis-ci.org/phpro/soap-client.svg)](http://travis-ci.org/phpro/soap-client)
[![Installs](https://img.shields.io/packagist/dt/phpro/soap-client.svg)](https://packagist.org/packages/phpro/soap-client/stats)
[![Packagist](https://img.shields.io/packagist/v/phpro/soap-client.svg)](https://packagist.org/packages/phpro/soap-client)

# General purpose PHP SOAP-client

Sick and tired of building crappy SOAP implementations?
 This package aims to help you with some common SOAP integration pains in PHP.
 Its goal is to make integrating with SOAP fun again!
 Let's inspect some of the pains that are solved by this package:

# Demo
<img src="https://raw.githubusercontent.com/wiki/phpro/soap-client/soap-client-demo-fast.gif" alt="Soap Client demo" width="100%"/> 

# About the soap client

By default, the SoapClient works with a mix of arrays, stdClasses and other scalar types. 
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
 
In lots of SOAP integrations, the logging, caching and Soap calls are in the same method.
 This makes your code hard to read and dependent on other classes / packages.

Creating a great OO SOAP client means that you'll have to create a lot of code.
 This can be a tedious task which can be automated. 
 That is why we've added the tools to automatically generate the SOAP objects from the XSD scheme inside the WSDL.
 It is even possible to specify your own code-generation rules and code assemblers or use one of our many built-in classes.

It is important keep your code clean. Therefore, we added an event-listener to your Soap client.
 You can hook in at every important step of the SOAP flow.
 This way it is possible to add logging, validation, caching and error handling with event subscribers. 
 Pretty cool right?!

Implementing SOAP extensions is a real pain in the ass.
 It forces you to overwrite core methods of the built-in SOAP client.
 If you ever had to implement WSA or WSSE in SOAP, you know that there is something wrong in the core.
 Therefore, we made it easy for you to extend our SOAP client. 
 You can specify which data transfer handler like e.g. Guzzle you want to use.
 Depending on the selected handler, 
 you can easily add support for SOAP extensions or advanced authentication through HTTP middlewares.
 
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

## Scafolding Wizard

Since life is too short to read documentation,
 we've added a scafolding wizard which will get you communicating with your SOAP server in no time!
All you need to do is:

```sh
composer require --dev zendframework/zend-code:^3.1.0
./vendor/bin/soap-client wizard
```

You can customize the generated code based on the manual installation pages in the next chapter.

## Getting your SOAP integration up and running

1. [Create your own SOAP client.](docs/client.md)
2. [Generate PHP classes based on SOAP types.](docs/cli/generate-types.md)
3. [Generate a class map](docs/cli/generate-classmap.md)
4. [Generate your own SOAP client.](docs/cli/generate-client.md)
5. [Generate a client factory.](docs/cli/generate-clientfactory.md)
6. [Use your SOAP client.](docs/usage.md)
7. [Test your SOAP client.](docs/testing.md)


## Advanced configuration

- [Add type converters.](docs/type-converter.md)
- [Listen to events.](docs/events.md)
  - [Logger plugin](docs/plugins/logger.md)
  - [Validator plugin](docs/plugins/validator.md)
  - [Caching plugin](docs/plugins/caching.md)
- [Specify your data transfer handler.](docs/handlers.md)
  - [SoapHandle](docs/handlers.md#soaphandle)
  - [HTTPlugHandle](docs/handlers.md#httplughandle)
- [Configure one or multiple HTTP middlewares.](docs/middlewares.md)
  - [BasicAuthMiddleware](docs/middlewares.md#basicauthmiddleware)
  - [NtlmMiddleware](docs/middlewares.md#ntlmmiddleware)
  - [WsaMiddleware](docs/middlewares.md#wsamiddleware)
  - [WsseMiddleware](docs/middlewares.md#wssemiddleware)
- [Select a WSDL Provider](docs/wsdl-providers.md)


## Customize the code generation

- [Configuration](docs/code-generation/configuration.md)
- [Specify generation `Rules`](docs/code-generation/rules.md)
- [Generate code through `Assemblers`](docs/code-generation/assemblers.md)

