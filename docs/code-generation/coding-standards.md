# Coding standards

The `CodingStandardsStrategyInterface` lets you customize how names are normalized during code generation.
This is useful when your project has specific naming conventions that differ from the defaults.

By default, `DefaultCodingStandardsStrategy` is used, which delegates to the built-in `Normalizer` methods.
This matches the existing behavior, so no changes are needed for existing users.

## Interface

```php
use Phpro\SoapClient\CodeGenerator\CodingStandards\CodingStandardsStrategyInterface;

interface CodingStandardsStrategyInterface
{
    /**
     * Convert SOAP type name to PHP class/enum name (e.g. "my-type" -> "MyType")
     */
    public function normalizeTypeName(string $name): string;

    /**
     * Convert SOAP operation name to PHP method name (e.g. "GetUsers" -> "getUsers")
     */
    public function normalizeOperationName(string $method): string;

    /**
     * Normalize XML namespace segment for PHP namespace (e.g. "my-prefix" -> "MyPrefix")
     * Return null to skip the segment.
     */
    public function normalizeNamespaceSegment(string $segment): ?string;

    /**
     * Normalize enum value to PHP enum case name (e.g. "some-value" -> "SomeValue")
     */
    public function normalizeEnumCaseName(string $value): string;

    /**
     * Generate accessor method name from prefix + property name
     * (e.g. "get" + "firstName" -> "getFirstName")
     */
    public function generatePropertyAccessorMethodName(string $prefix, string $property): string;

    /**
     * Normalize a parameter name for constructors and setters.
     * Input is the fixed property name. Output is the parameter name for PHP code.
     * This enables named arguments: new Foo(fooBar: 'x') while property stays $foo_bar.
     */
    public function normalizeParameterName(string $propertyName): string;
}
```

## Configuration

Configure coding standards via `Config::setCodingStandards()`.
The generated config uses `return ($config = Config::create())` so that `$config` can be referenced in nested calls:

```php
use Phpro\SoapClient\CodeGenerator\Config\Config;
use Phpro\SoapClient\CodeGenerator\Config\Destination;
use Phpro\SoapClient\CodeGenerator\Config\TypeNamespaceMap;
use Phpro\SoapClient\CodeGenerator\TypeNamespaceMap\Strategy\PrefixBasedTypeNamespaceStrategy;

return ($config = Config::create())
    ->setCodingStandards(new MyCodingStandards())
    ->setEngine(...)
    ->setTypeNamespaceMap(
        TypeNamespaceMap::create(new Destination('src/Type', 'App\\Type'))
            ->withStrategy(new PrefixBasedTypeNamespaceStrategy($config->getCodingStandards()))
    )
    ->setClient(...)
    ->setClassMap(...)
;
```

When using `PrefixBasedTypeNamespaceStrategy`, pass the coding standards to its constructor so that namespace segments are normalized using the same strategy.

## Creating a custom strategy

Implement `CodingStandardsStrategyInterface` to define your own naming conventions:

```php
use Phpro\SoapClient\CodeGenerator\CodingStandards\CodingStandardsStrategyInterface;

class MyCodingStandards implements CodingStandardsStrategyInterface
{
    public function normalizeTypeName(string $name): string
    {
        // e.g. prefix all types
        return 'Soap' . ucfirst($name);
    }

    public function normalizeOperationName(string $method): string
    {
        return lcfirst($method);
    }

    public function normalizeNamespaceSegment(string $segment): ?string
    {
        return ucfirst($segment);
    }

    public function normalizeEnumCaseName(string $value): string
    {
        return strtoupper($value);
    }

    public function generatePropertyAccessorMethodName(string $prefix, string $property): string
    {
        return $prefix . ucfirst($property);
    }

    public function normalizeParameterName(string $propertyName): string
    {
        return lcfirst($propertyName);
    }
}
```

## Accessing coding standards in custom assemblers

Inside a custom assembler, the `CodeGeneratorContext` is available through the assembler context:

```php
use Phpro\SoapClient\CodeGenerator\Assembler\AssemblerInterface;
use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\CodeGenerator\Context\PropertyContext;

class MyAssembler implements AssemblerInterface
{
    public function canAssemble(ContextInterface $context): bool
    {
        return $context instanceof PropertyContext;
    }

    public function assemble(ContextInterface $context): void
    {
        assert($context instanceof PropertyContext);

        $codingStandards = $context->getCodeGeneratorContext()->codingStandards;
        $typeNamespaceMap = $context->getCodeGeneratorContext()->typeNamespaceMap;

        // Use coding standards to generate a custom method name:
        $methodName = $codingStandards->generatePropertyAccessorMethodName(
            'has',
            $context->getProperty()->getName()
        );

        // Or use the convenience methods on Property:
        $getterName = $context->getProperty()->methodName('get');
        $paramName = $context->getProperty()->parameterName();

        // ...
    }
}
```
