# Convert SOAP types

Some exotic XSD types are hard to transform to PHP objects.
 A typical example are dates: some people like it as a timestamp, some want it as a DateTime, ...
 By adding custom TypeConverters, it is possible to convert a WSDL type to / from a PHP type.
 
These TypeConverters are added by default:

- DateTimeTypeConverter 
- DateTypeConverter
- DoubleTypeConverter
- DecimalTypeConverter

You can also create your own converter by implementing the `TypeConverterInterface`. For example:

```php
class MyCustomConverter implements TypeConverterInterface
{
    // Implement methods...
}
```
