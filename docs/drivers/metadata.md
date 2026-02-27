# Driver metadata

### Duplicate types

XSDs can have both internal and external types making that the names of the type can be non-unique in a given XML namespace.
It might not be possible to generate unique types for all types in the WSDL.
Therefore, we added some strategies to deal with duplicate types by default.

This can be configured in the [client configuration](/docs/code-generation/configuration.md):

**IntersectDuplicateTypesStrategy**

Enabled by default when using `Config::create()`.

This duplicate types strategy will merge all duplicate types into one big type which contains all properties.

```php
use Phpro\SoapClient\CodeGenerator\Config\Config;
use Phpro\SoapClient\Soap\Metadata\Manipulators\DuplicateTypes\IntersectDuplicateTypesStrategy;

return Config::create()
    //...
    ->setDuplicateTypeIntersectStrategy(
        IntersectDuplicateTypesStrategy::create()
    )
    // ...
```

**RemoveDuplicateTypesStrategy**

This duplicate types strategy will remove all duplicate types it finds.

```php
use Phpro\SoapClient\CodeGenerator\Config\Config;
use Phpro\SoapClient\Soap\Metadata\Manipulators\DuplicateTypes\RemoveDuplicateTypesStrategy;

return Config::create()
    //...
    ->setDuplicateTypeIntersectStrategy(
        RemoveDuplicateTypesStrategy::create()
    )
    // ...
```

**Namespace-aware duplicate detection**

The duplicate type strategies are namespace-aware when used with `TypeNamespaceMap`.
When you configure multiple XML namespaces to map to different PHP namespaces,
types with the same name in different XML namespaces will NOT be considered duplicates
because they will be generated into separate PHP namespaces.

For example, if you have `AccidentItem` in both `http://ns-a.com` and `http://ns-b.com`,
and you map these to `App\Type\A` and `App\Type\B` respectively,
the strategies will treat them as separate types (`App\Type\A\AccidentItem` and `App\Type\B\AccidentItem`).

```php
use Phpro\SoapClient\CodeGenerator\Config\Config;
use Phpro\SoapClient\CodeGenerator\Config\Destination;
use Phpro\SoapClient\CodeGenerator\Config\TypeNamespaceMap;
use Phpro\SoapClient\Soap\Metadata\Manipulators\DuplicateTypes\IntersectDuplicateTypesStrategy;

return Config::create()
    ->setTypeNamespaceMap(
        TypeNamespaceMap::create(new Destination('src/Type', 'App\\Type'))
            ->withMapping('http://ns-a.com', new Destination('src/Type/A', 'App\\Type\\A'))
            ->withMapping('http://ns-b.com', new Destination('src/Type/B', 'App\\Type\\B'))
    )
    ->setDuplicateTypeIntersectStrategy(
        IntersectDuplicateTypesStrategy::create()
    )
    // ...
```

The `create()` factory method returns a closure that receives the `CodeGeneratorContext` from the configuration,
enabling the strategy to determine target PHP namespaces for each type.

### Type replacements

Depending on what [SOAP encoders](https://github.com/php-soap/encoding#encoder) you configure, you might want to replace some types with other types.
Take following example:

By default, a "date" type from the XSD namespace `http://www.w3.org/2001/XMLSchema` will be converted to a `DateTimeImmutable` object.
However, if you configure an encoder that does not support `DateTimeImmutable`,
you might want to replace it with a `int` type that represents the amount of seconds since the unix epoch.

This can be configured in the [client configuration](/docs/code-generation/configuration.md):

```php
use Phpro\SoapClient\CodeGenerator\Config\Config;
use Phpro\SoapClient\Soap\Metadata\Manipulators\TypeReplacer\TypeReplacers;

return Config::create()
    //...
    ->setTypeReplacements(
        TypeReplacers::defaults()
            ->add(new MyDateReplacer())
    )
    // ...
```

The `MyDateReplacer` class should implement the `TypeReplacerInterface` and should return the correct type for the given type.

```php
use Phpro\SoapClient\Soap\Metadata\Manipulators\TypeReplacer\TypeReplacer;
use Soap\Engine\Metadata\Model\XsdType;
use Soap\WsdlReader\Metadata\Predicate\IsOfType;use Soap\Xml\Xmlns;

final class MyDateReplacer implements TypeReplacer
{
    public function __invoke(XsdType $xsdType): XsdType
    {
        $check = new IsOfType(Xmlns::xsd()->value(), 'date');
        if (!$check($xsdType)) {
            return $xsdType;
        }

        return $xsdType->copy('int')->withBaseType('int');
    }
}
```

This way, the generated code will use the `int` type instead of the `DateTimeImmutable` type for the `date` type in the XSD.

The TypeReplacers contain a default set of type replacements that are being used to improve the generated code:

* `array` for SOAP 1.1 and 1.2 Arrays
* `object` for SOAP 1.1 Objects
* `array` for Apache Map types


#### Using an existing 3rd party class

If you want to replace a type with an existing 3rd party class, you can copy the type with the fully qualified class name of the 3rd party class.
That way, the property type of the generated code will be set to the newly linked class.
This type replacer is only being used for generating code.
You will still need to implement the logic to convert the type to the 3rd party class in a [custom SOAP encoder](https://github.com/php-soap/encoding#encoder).

```php
use Phpro\SoapClient\Soap\Metadata\Manipulators\TypeReplacer\TypeReplacer;
use Soap\Engine\Metadata\Model\XsdType;
use Soap\WsdlReader\Metadata\Predicate\IsOfType;

class GuidTypeReplacer implements TypeReplacer
{
    public function __invoke(XsdType $xsdType): XsdType
    {
        $check = new IsOfType('http://microsoft.com/wsdl/types/', 'guid');
        if (!$check($xsdType)) {
            return $xsdType;
        }

        return $xsdType->copy(\Ramsey\Uuid\UuidInterface::class);
    }
}
```

**Note**: If you want to use a built-in PHP class, you will need to prefix it with a backslash. For example: `\DateInterval`.
