# Generating class maps

Before you can generate code, you'll need to add some additional dev dependencies to your project:
```sh
composer require --dev laminas/laminas-code:^3.1.0
```

When the value-objects are generated, we need to tell SOAP about how the PHP classes are mapped to the XSD types.
 This is done by a class map, which can be a really boring manual task.
 Luckily a class map generator is added, which you can use to parse the classmap from the WSDL.

```sh
$ ./vendor/bin/soap-client generate:classmap                                                                                                                                    [16:13:31]
Usage:
  generate:classmap [options]

Options:
      --config=CONFIG   The location of the soap code-generator config file
  -o, --overwrite       Makes it possible to overwrite by default
  -h, --help            Display this help message
  -q, --quiet           Do not output any message
  -V, --version         Display this application version
      --ansi            Force ANSI output
      --no-ansi         Disable ANSI output
  -n, --no-interaction  Do not ask any interactive question
  -v|vv|vvv, --verbose  Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug


```

To generate a classmap, following values need to be set in your config:
```
->setClassMapName('MyClassmap')
->setClassMapNamespace('Myapp\\MyclassMap')
->setClassMapDestination('src/myapp/myclassmap')
```

Where the name is the class name you want to give your classmap, the namespace is where this class should reside in and the destination in the relative path from the project root where the file should be put.


Options:

- **config**: A [configuration file](../code-generation/configuration.md) is required to build the classmap. 

 
Example output:

```php
<?php

namespace Myapp\Example\Classmap;

use ...

class OrderClassMap
{

    public static function getCollection() : \Phpro\SoapClient\Soap\ClassMap\ClassMapCollection
    {
        return new ClassMapCollection([
            new ClassMap('CreateOrder', Type\Example1::class),
            new ClassMap('CardOrder', Type\Example2::class),
            new ClassMap('OrderDetails', Type\Example3::class)
        ]);
    }
}
```

Next: [Generate your own SOAP client.](generate-client.md)
