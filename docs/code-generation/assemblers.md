# Code assemblers

Code assemblers are a thin layer above [zend-code](https://github.com/zendframework/zend-code).
There are a lot of built-in assemblers but it is also possible to create your own assembler 
to generate the code you want to add to the generated SOAP types.
 
# Built-in assemblers

- [ClassMapAssembler](#classmapassembler)
- [ConstructorAssembler](#constructorassembler)
- [FinalClassAssembler](#finalclassassembler)
- [FluentSetterAssembler](#fluentsetterassembler)
- [GetterAssembler](#getterassembler)
- [InterfaceAssembler](#interfaceassembler)
- [IteratorAssembler](#iteratorassembler)
- [PropertyAssembler](#propertyassembler)
- [RequestAssembler](#requestassembler)
- [ResultAssembler](#resultassembler)
- [ResultProviderAssembler](#resultproviderassembler)
- [SetterAssembler](#setterassembler)
- [TraitAssembler](#traitassembler)
- [UseAssembler](#useassembler)


## ClassMapAssembler

The `ClassMapAssembler` is activaded by default and is used during the `generate:classmap` command.

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


## ConstructorAssembler

The `ConstructorAssembler can be used to add a constructor with all the class properties to the generated class.

Example output:

```php
    /**
     * Constructor
     *
     * @var string $prop1
     * @var int $prop2
     */
    public function __construct($prop1, $prop2)
    {
        $this->prop1 = $prop1;
        $this->prop2 = $prop2;
    }
```


## FluentSetterAssembler

The `FluentSetterAssembler` will add a setter method to the generated class. The method will return the current instance to enable chaining.

Example output:

```php
    /**
     * @param string $prop1
     * @return $this
     */
    public function setProp1($prop1)
    {
        $this->prop1 = $prop1;
        return $this;
    }
```

## FinalClassAssembler

The `FinalClassAssembler` can be used to mark a generated class as final.

Example output:

```php

final class MyType
{


}
```

## GetterAssembler

The `GetterAssembler` will add a getter method to the generated class.

Example output:

```php
    /**
     * @return string
     */
    public function getProp1()
    {
        return $this->prop1;
    }
```

## InterfaceAssembler

The `InterfaceAssembler` can be used to add a specific interface to the generated class.

Example output:

```php

use Iterator;

class MyType implements Iterator
{


}
```


## IteratorAssembler

The `IteratorAssembler` can be used for SOAP types that contain a list of another SOAP type.
This assembler will make it easy to iterate over the types.

Example output:

```php
use IteratorAggregate;

class MyType implements IteratorAggregate
{

    /**
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator(is_array($this->prop1) ? $this->prop1 : []);
    }


}
```


## PropertyAssembler

This `PropertyAssembler` is enabled by default and is used during the `generate:types` command.

Example output:

```php
    /**
     * @var string
     */
    protected $prop1 = null;
```


## RequestAssembler

The `RequestAssembler` can be used to add the `RequestInterface` to a generated class.

Example output:

```php
use Phpro\SoapClient\Type\RequestInterface;

class MyType implements RequestInterface
{
}
```


## ResultAssembler

The `ResultAssembler` can be used to add the `ResultInterface` to a generated class.

Example output:

```php
use Phpro\SoapClient\Type\ResultInterface;

class MyType implements ResultInterface
{
}
```


## ResultProviderAssembler

The `ResultProviderAssembler` can be used to add the `ResultProviderInterface` to a generated class.

Example output:

```php
use Phpro\SoapClient\Type\ResultProviderInterface;

class MyType implements ResultProviderInterface
{

    /**
     * @return SomeClass|Phpro\SoapClient\Type\ResultInterface
     */
    public function getResult()
    {
        return $this->prop1;
    }
}
```


## SetterAssembler

The `SetterAssembler` will add a setter method to the generated class.

Example output:

```php
    /**
     * @param string $prop1
     */
    public function setProp1($prop1)
    {
        $this->prop1 = $prop1;
    }
```


## TraitAssembler

The `TraitAssembler` can be used to add a specific trait to the generated class. An alias can be used by passing it in as second argument.

Example output:

```php

use MyTrait;

class MyType
{

    use MyTrait

}
```


## UseAssembler

The `UseAssembler` can be used to add usage statements to the generated class. Often used internally to add uses for interfaces or traits. An alias can be used by passing it in as second argument.

Example output:

```php

use MyTrait as TraitAlias;

class MyType
{


}
```


# Creating your own Assembler

Creating your own Assembler is pretty easy. 
The only thing you'll need to do is implementing the `AssemblerInterface`.
You can use the [zend-code](https://github.com/zendframework/zend-code) `ClassGenerator` and `FileGenerator` to manipulate your code.

```php
/**
 * Interface AssemblerInterface
 *
 * @package Phpro\SoapClient\CodeGenerator\Assembler
 */
interface AssemblerInterface
{

    /**
     * @param ContextInterface $context
     *
     * @return bool
     */
    public function canAssemble(ContextInterface $context);

    /**
     * Assembles pieces of code.
     *
     * @param ContextInterface $context
     * @throws AssemblerException
     */
    public function assemble(ContextInterface $context);
}
```

Possible contexts:

- `ClassMapContext`: Triggered during the `generate:classmap` command.
- `TypeContext`: Triggered during the `generate:types` command for every type in the SOAP scheme.
- `PropertyContext`: Triggered during the `generate:types` command for every property in a SOAP type.
