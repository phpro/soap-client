<?php

namespace Phpro\SoapClient\CodeGenerator\CodingStandards;

interface CodingStandardsStrategyInterface
{
    /**
     * Convert SOAP type name to PHP class/enum name (e.g. "my-type" -> "MyType")
     *
     * @param non-empty-string $name
     * @return non-empty-string
     */
    public function normalizeTypeName(string $name): string;

    /**
     * Convert SOAP operation name to PHP method name (e.g. "GetUsers" -> "getUsers")
     *
     * @param non-empty-string $method
     * @return non-empty-string
     */
    public function normalizeOperationName(string $method): string;

    /**
     * Normalize XML namespace segment for PHP namespace (e.g. "my-prefix" -> "MyPrefix")
     */
    public function normalizeNamespaceSegment(string $segment): ?string;

    /**
     * Normalize enum value to PHP enum case name (e.g. "some-value" -> "SomeValue")
     *
     * @return non-empty-string
     */
    public function normalizeEnumCaseName(string $value): string;

    /**
     * Generate accessor method name from prefix + property name (e.g. "get" + "firstName" -> "getFirstName")
     *
     * @param non-empty-string $prefix
     * @param non-empty-string $property
     * @return non-empty-string
     */
    public function generatePropertyAccessorMethodName(string $prefix, string $property): string;

    /**
     * Normalize a parameter name (for constructors, setters, etc.)
     * Input is the fixed property name. Output is the parameter name for PHP code.
     * Enables named arguments: new Foo(fooBar: 'x') while property stays $foo_bar.
     *
     * @param non-empty-string $propertyName
     * @return non-empty-string
     */
    public function normalizeParameterName(string $propertyName): string;
}
