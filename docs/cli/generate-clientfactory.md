# Generate a base client factory

To make things a little easier to get started a client factory generator method is available.

```bash
vendor/bin/soap-client generate:clientfactory

Usage:
  generate:clientfactory [options]

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

Options:

- **config**: A [configuration file](../code-generation/configuration.md) is required to build the classmap. 

The factory will be put in the same namespace and directory as the client, and use the same name as the client, appended by Factory.

Example output:

```php
<?php

namespace App\Client;

use App\Client\Client;
use App\Order\OrderClassmap;
use Phpro\SoapClient\ClientFactory as PhproClientFactory;
use Phpro\SoapClient\ClientBuilder;

class ClientFactory
{

    public static function factory(string $wsdl) : \App\Client
    {
        $clientFactory = new PhproClientFactory(Client::class);
        $clientBuilder = new ClientBuilder($clientFactory, $wsdl, []);
        $clientBuilder->withClassMaps(OrderClassmap::getCollection());

        return $clientBuilder->build();
    }
}


```

You can then tweak this class to fit your needs.