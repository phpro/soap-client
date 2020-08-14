# Generating Value-objects

Before you can generate code, you'll need to add some additional dev dependencies to your project:
```sh
composer require --dev laminas/laminas-code:^3.1.0
```

Basic value-objects can be generated automatically.

```sh
$ ./vendor/bin/soap-client generate:types
                                                                                                                                       [16:13:38]
Usage:
  generate:types [options]

Options:
      --config=CONFIG   The location of the soap code-generator config file
  -o, --overwrite       Makes it possible to overwrite by default

```

This generator will read all XSD types from the provided WSDL and convert it to PHP classes.
 You can specify a namespace and a location where the classes will be stored.
 The properties from the XSD will be added as protected properties to the value-objects.
 When the classes already exist, a patch operation is performed and a backup file is created. 
 This way your custom code will always remain available.

Keep in mind that the WSDL must provide all XSD types for the generation of value-objects.
 Some exotic SOAP services don't provide much information. For example: they will return an XML string which needs to be parsed manually.
 These WSDLs can only be parsed as far as the XSD information goes.
 All other information needs to be added manually, or by a custom class generator.

Options:

- **config**: A [configuration file](../code-generation/configuration.md) is required to build the types. 
- **overwrite**: The soap-client overrides a file that cannot be patched without asking for confirmation.


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

This can be done by specifying some code generation [rules](../code-generation/rules.md) and [assemblers](../code-generation/assemblers.md).

Next:  [Generate a class map](generate-classmap.md)
