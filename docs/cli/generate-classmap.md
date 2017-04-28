# Generating class maps

Before you can generate code, you'll need to add some additional dev dependencies to your project:
```sh
composer require --dev zendframework/zend-code:^3.0.4
```

When the value-objects are generated, we need to tell SOAP about how the PHP classes are mapped to the XSD types.
 This is done by a class map, which can be a really boring manual task.
 Luckily a class map generator is added, which you can use to parse the classmap from the WSDL.

```sh
$ soap-client generate:classmap                                                                                                                                    [16:13:31]
Usage:
  generate:classmap [options]

Options:
      --config=CONFIG   The location of the soap code-generator config file

```

This command will generate a class map and display it on the screen.
 You will just need to copy it and next paste it in the `ClientBuilder`.


Options:

- **config**: A [configuration file](../code-generation/configuration.md) is required to build the classmap. 

 
Example output:

```php
<?php

use Phpro\SoapClient\Soap\ClassMap\ClassMapCollection;
use Phpro\SoapClient\Soap\ClassMap\ClassMap;

new ClassMapCollection([
        new ClassMap('HelloWorldRequest', \HelloWorldRequest::class),
        new ClassMap('HelloWorldResponse', \HelloWorldResponse::class),
        new ClassMap('Greeting', \Greeting::class),
]);
```
