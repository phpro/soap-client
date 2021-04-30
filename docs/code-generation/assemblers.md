# Code assemblers

Code assemblers are a thin layer above [laminas-code](https://github.com/laminas/laminas-code).
There are a lot of built-in assemblers but it is also possible to create your own assembler 
to generate the code you want to add to the generated SOAP types.
 
# Built-in assemblers

- [ClassMapAssembler](#classmapassembler)
- [ConstructorAssembler](#constructorassembler)
- [ExtendAssembler](#extendassembler)
- [FinalClassAssembler](#finalclassassembler)
- [FluentSetterAssembler](#fluentsetterassembler)
- [GetterAssembler](#getterassembler)
- [ImmutableSetterAssembler](#immutablesetterassembler)
- [InterfaceAssembler](#interfaceassembler)
- [IteratorAssembler](#iteratorassembler)
- [JsonSerializableAssembler](#jsonserializableassembler)
- [PropertyAssembler](#propertyassembler)
- [RequestAssembler](#requestassembler)
- [ResultAssembler](#resultassembler)
- [ResultProviderAssembler](#resultproviderassembler)
- [SetterAssembler](#setterassembler)
- [TraitAssembler](#traitassembler)
- [UseAssembler](#useassembler)


## ClassMapAssembler

The `ClassMapAssembler` is activated by default and is used during the `generate:classmap` command.

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

Generating type-hints is disabled by default, but can be enabled by passing `ConstructorAssemblerOptions` instance to the constructor with the option `withTypeHints` set to true.

Example
```php
new ConstructorAssembler((new ConstructorAssemblerOptions())->withTypeHints())
```

```php
    /**
     * Constructor
     *
     * @var string $prop1
     * @var int $prop2
     */
    public function __construct(string $prop1, int $prop2)
    {
        $this->prop1 = $prop1;
        $this->prop2 = $prop2;
    }
```

Generating doc blocks is enabled by default, but can be disabled by passing `ConstructorAssemblerOptions` instance to the constructor with the option `withDocBlocks` set to false. This is normally used in conjunction with `withTypeHints`

Example
```php
new ConstructorAssembler((new ConstructorAssemblerOptions())->withDocBlocks(false)->withTypeHints())
```

```php
    public function __construct(string $prop1, int $prop2)
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

Generating doc blocks is enabled by default, but can be disabled by passing `FluentSetterAssemblerOption` instance to the constructor with the option `withDocBlocks` set to false. This is normally used in conjunction with `withTypeHints`

Example
```php
new FluentSetterAssembler((new FluentSetterAssemblerOptions())->withDocBlocks(false)->withTypeHints())
```

```php
    /**
     * @param string $prop1
     * @return $this
     */
    public function setProp1(string $prop1)
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
## ExtendAssembler

The `ExtendAssembler` will add a parent class to the generated class.

Example output:

```php

class MyType extends DType
{


}
```

## GetterAssembler

The `GetterAssembler` will add a getter method to the generated class.
For boolean types you can opt to use the 'is' function prefix instead of 'get' by enabling this in the constructor.

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

Generating doc blocks is enabled by default, but can be disabled by passing `GetterAssemblerOptions` instance to the constructor with the option `withDocBlocks` set to false. This is normally used in conjunction with `withTypeHints`

Example
```php
new GetterAssembler((new GetterAssemblerOptions())->withDocBlocks(false)->withTypeHints())
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

## JsonSerializableAssembler

The `JsonSerializableAssembler` can be used if you want to JSON serialize your SOAP objects. 
This could be handy for logging JSON serialized request / response data which makes your logs smaller.

Example output:

```php
use JsonSerializable;

class MyType implements JsonSerializable
{

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'prop1' => \$this->prop1,
            'prop2' => \$this->prop2,
        ];
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
    private $prop1 = null;
```

You can adjust the visibility of the property by injecting the visibility in the constructor.

```php
new PropertyAssembler(PropertyGenerator::VISIBILITY_PROTECTED)
```

Please note that the default ruleset has a visibility of private.
If you want to override this, you will have to override all rules by calling `Phpro\SoapClient\CodeGenerator\Config\Config::setRuleSet`.


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
use Phpro\SoapClient\Type\ResultInterface;

class MyType implements ResultProviderInterface
{

    /**
     * @return SomeClass|ResultInterface
     */
    public function getResult() : \Phpro\SoapClient\Type\ResultInterface
    {
        return $this->prop1;
    }
}
```

It is also possible to add an optional `wrapperClass` to the constructor.
This way, the result is wrapped with a class you specified.

Example output:

```php
namespace MyNamespace;

use Phpro\SoapClient\Type\ResultProviderInterface;
use Phpro\SoapClient\Type\MixedResult

class MyType implements ResultProviderInterface
{

    /**
     * @return MixedResult
     */
    public function getResult() : \Phpro\SoapClient\Type\ResultInterface
    {
        return new MixedResult($this->prop1);
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

Generating doc blocks is enabled by default, but can be disabled by passing `SetterAssemblerOptions` instance to the constructor with the option `withDocBlocks` set to false. This is normally used in conjunction with `withTypeHints`

Example
```php
new SetterAssembler((new SetterAssemblerOptions())->withDocBlocks(false)->withTypeHints())
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

## ImmutableSetterAssembler

The `ImmutableSetterAssembler` generates immutable setters that return a new instance with the new value set.
Used to create variations of the same base instance, without modifying the original values.

Example output:

```php
    /**
     * @param string $prop1
     * @return MyType
     */
    public function withProp1($prop1)
    {
        $new = clone $this;
        $new->prop1 = $prop1;

        return $new;
    }
```

Generating doc blocks is enabled by default, but can be disabled by passing `ImmutableSetterAssemblerOptions` instance to the constructor with the option `withDocBlocks` set to false. This is normally used in conjunction with `withTypeHints`

Example
```php
new ImmutableSetterAssembler((new ImmutableSetterAssemblerOptions())->withDocBlocks(false)->withTypeHints())
```

# Creating your own Assembler

Creating your own Assembler is pretty easy. 
The only thing you'll need to do is implementing the `AssemblerInterface`.
You can use the [laminas-code](https://github.com/laminas/laminas-code) `ClassGenerator` and `FileGenerator` to manipulate your code.

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
