# Code assemblers

Code assemblers are a thin layer above [zend-code](https://github.com/zendframework/zend-code).
You can create your own assembler to generate the code you want to add to the generated SOAP types.

 
# Built-in assemblers

- [ClassMapAssembler](#classmapassembler)
- [GetterAssembler](#GetterAssembler)
- [InterfaceAssembler](#InterfaceAssembler)
- [IteratorAssembler](#IteratorAssembler)
- [PropertyAssembler](#PropertyAssembler)
- [RequestAssembler](#RequestAssembler)
- [ResultAssembler](#ResultAssembler)
- [ResultProviderAssembler](#ResultProviderAssembler)
- [SetterAssembler](#SetterAssembler)


## ClassMapAssembler

The `ClassMapAssembler` is activaded by default and is used during the `generate:classmap` command.

Example output:

```php
<?php

return new ClassMapCollection(
    [
        new ClassMap('HelloWorldRequest', \HelloWorldRequest::class),
        new ClassMap('HelloWorldResponse', \HelloWorldResponse::class),
        new ClassMap('Greeting', \Greeting::class),
    ]
);
```

## ConstructorAssembler

TODO

## GetterAssembler

TODO

## InterfaceAssembler

TODO

## IteratorAssembler

TODO

## PropertyAssembler

TODO

## RequestAssembler

TODO

## ResultAssembler

TODO

## ResultProviderAssembler

TODO

## SetterAssembler

TODO
