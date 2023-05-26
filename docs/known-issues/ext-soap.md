# Known issues in ext-soap

- [Duplicate typenames](#duplicate-typenames)
- [Enumerations](#enumerations)

Isn't your issue listed below? Feel free to provide additional issues in a functional test.


**[Find out how you can help out here 💚](https://github.com/php-soap/.github/blob/main/HELPING_OUT.md)**


## Duplicate typenames

When there are 2 types with the same name in different XML namespaces,
ext-soap is not able to link those types to 2 different PHP objects.
This package will generate the code for the last detected type in the WSDL.

Suggested workaround:

1. Use one of [the built-in duplicate types strategies](../drivers/metadata.md#duplicate-types)
2. Manually determine type converters for the various classes:
    - Manually create the missing classes.
    - Determine which is the most important type and use that one in the classmap.
    - You can use the type converters for the other type(s) with the same name.
    - You'll need to manually parse the XML and link it to an object.

```php
$soapOptions = [
    'classmap' => [
        'Store' => MostImportantStore::class,
    ],
    'typemap' => [
        [
            'type_name' => 'Store',
            'type_ns' => 'http://......lessimportantstore',
            'from_xml' => function($xml) {
                $data = simplexml_load_string($xml);
    
                return LessImportantStore::fromXml($data);
            },
        ],
    ],
];
```

Alternative workaround:

- Merge all properties of all types in one big class.
- Map the master class to the xsd type in the classmap
- This might be a bad idea of the objects are not very similar.

More information:

- [Code in php-src](https://github.com/php/php-src/blob/php-7.2.10/ext/soap/php_encoding.c#L468)
- [Functional test](../../test/PhproTest/SoapClient/Functional/ExtSoap/Encoding/DuplicateTypenamesTest.php)


**[Find out how you can help out here 💚](https://github.com/php-soap/.github/blob/main/HELPING_OUT.md)**

## Enumerations

It is possible that the WSDL file contains `xsd:enumeration` elements.
Since PHP does not have an internal `enum` type,
ext-soap will transform the data to the type that is determined in the `xsd:restriction` section.
The soap client will never try to validate the input against the configured enumerations inside the WSDL.

We've added basic support for enumerations in this package so that issues can be resolved at static analysis level.
However, during runtime, it is not possible to validate these enumerations without creating a custom typemap.

For example:
```xml
<xsd:simpleType name="PhoneTypeEnum">
    <xsd:restriction base="xsd:string">
        <xsd:enumeration value="Home"/>
        <xsd:enumeration value="Office"/>
        <xsd:enumeration value="Gsm"/>
    </xsd:restriction>
</xsd:simpleType>
``` 

- It is perfectly possible to pass a value of "InvalidData" to the server through the soap-client.
- Internally, ext-soap will typehint this enum as a string so there is no complex type available.
- You won't be able to access the available options without manually parsing the WSDL file.
- If you do want to use a custom class for the enumerations type, you can create a type converter like this:

```php
enum PhoneTypeEnum : string {
    case HOME = 'Home';
    case GSM = 'Gsm';
    case OFFICE = 'Office';
}

$soapOptions = [
    'typemap' => [
        [
            'type_name' => 'PhoneTypeEnum',
            'type_ns' => 'http://soapinterop.org/xsd',
            'from_xml' => function($xml) {
                $doc = new \DOMDocument();
                $doc->loadXML($xml);

                return PhoneTypeEnum::from($doc->textContent);
            },
            'to_xml' => function(PhoneTypeEnum $enum) {
                return sprintf('<PhoneTypeEnum>%s</PhoneTypeEnum>', $enum->value);
            },
        ],
    ],
];
```

More information:
- [Functional test](../../test/PhproTest/SoapClient/Functional/ExtSoap/Encoding/EnumTest.php)
- [Lack of validation in php-src](https://github.com/php/php-src/blob/php-7.2.10/ext/soap/php_encoding.c#L3172-L3200)

**[Find out how you can help out here 💚](https://github.com/php-soap/.github/blob/main/HELPING_OUT.md)**
