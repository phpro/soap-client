# Generating clients

Before you can generate code, you'll need to add some additional dev dependencies to your project:
```sh
composer require --dev zendframework/zend-code:^3.1.0
```

A client with type and return type hints can be generated.

```sh
$ ./vendor/bin/soap-client generate:client                                                                                                                                    [16:13:31]
Usage:
  generate:classmap [options]

Options:
      --config=CONFIG   The location of the soap code-generator config file

```

This command will generate a class map and display it on the screen.
 You will just need to copy it and next paste it in the `ClientBuilder`.


Options:

- **config**: A [configuration file](../code-generation/configuration.md) is required to build the classmap. 
