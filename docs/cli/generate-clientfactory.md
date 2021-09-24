# Generate a base client factory

To make things a little easier to get started a client factory generator method is available.

```bash
vendor/bin/soap-client generate:clientfactory

Usage:
  generate:clientfactory [options]

Options:
      --config=CONFIG   The location of the soap code-generator config file
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

More advanced client factory:

```php
<?php

use Http\Client\Common\PluginClient;
use Http\Discovery\Psr18ClientDiscovery;
use Phpro\SoapClient\Soap\ExtSoap\Metadata\Manipulators\DuplicateTypes\IntersectDuplicateTypesStrategy;
use Phpro\SoapClient\Soap\Metadata\MetadataOptions;
use Soap\Psr18Transport\Psr18Transport;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Phpro\SoapClient\Soap\ExtSoap\DefaultEngineFactory;
use Soap\ExtSoapEngine\ExtSoapOptions;
use Phpro\SoapClient\Caller\EventDispatchingCaller;
use Phpro\SoapClient\Caller\EngineCaller;

class CalculatorClientFactory
{
    public static function factory(string $wsdl) : CalculatorClient
    {
        $engine = DefaultEngineFactory::create(
            ExtSoapOptions::defaults($wsdl, [])
                ->withClassMap(CalculatorClassmap::getCollection()),
            Psr18Transport::createForClient(
                new PluginClient(
                    Psr18ClientDiscovery::find(),
                    [$plugin1, $plugin2]
                )
            ),
            MetadataOptions::empty()->withTypesManipulator(
                new IntersectDuplicateTypesStrategy()
            )
        );

        $eventDispatcher = new EventDispatcher();
        $caller = new EventDispatchingCaller(new EngineCaller($engine), $eventDispatcher);

        return new CalculatorClient($caller);
    }
}


```

You can then tweak this class to fit your needs.

Here you can find some bookmarks for changing the factory:

- [Configuring ExtSoapOptions](https://github.com/php-soap/ext-soap-engine/#configuration-options)
- [Listening to events](../events.md)
- [Configuring the engine](https://github.com/php-soap/engine)
- [Using HTTP middleware](https://github.com/php-soap/psr18-transport/#middleware) 

Next: [Use your SOAP client.](/docs/usage.md)
