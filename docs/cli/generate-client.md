# Generating clients

Before you can generate code, you'll need to add some additional dev dependencies to your project:
```sh
composer require --dev laminas/laminas-code:^3.1.0
```

A client with type and return type hints can be generated.

```sh
$ ./vendor/bin/soap-client generate:client                                                                                                                                    [16:13:31]
Usage:
  generate:client [options]

Options:
      --config=CONFIG   The location of the soap code-generator config file

```

This command will generate a client based on the client setings in the configuration.
This client will contain all methods used to initiate calls and uses type hinted parameters and return types.

Options:

- **config**: A [configuration file](../code-generation/configuration.md) is required to build the classmap. 

Next: [Generate a client factory.](generate-clientfactory.md)
