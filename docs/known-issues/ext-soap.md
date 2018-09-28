# Known issues in ext-soap

- [Base64 binary](#base64-binary)
- [Duplicate typenames](#duplicate-typenames)
- [Enumerations](#enumerations)
- [SimpleContent](#simplecontent)

## Base64 binary

By default, ext-soap will encode / decode `xsd:base64Binary` types.
This means that you will always be working with the decoded values when using ext-soap.
When you do require the raw values, you can create a custom typemap:

```php
$soapOptions = [
    'typemap' => [
        [
            'type_name' => 'base64Binary',
            'type_ns' => 'http://www.w3.org/2001/XMLSchema',
            'from_xml' => function($xml) {
                $doc = new \DOMDocument();
                $doc->loadXML($xml);

                return $doc->textContent;
            },
            'to_xml' => function($raw) {
                return sprintf('<base64Binary>%s</base64Binary>', $raw);
            },
        ],
    ],
];
```

More information:

- [Code in php-src](https://github.com/php/php-src/blob/php-7.2.10/ext/soap/php_encoding.c#L175)
- [Functional test](../../test/PhproTest/SoapClient/Functional/Encoding/Base64BinaryTest.php)


## Duplicate typenames

When there are 2 types with the same name in different XML namespaces,
ext-soap is not able to link those types to 2 different PHP objects.
This package will generate the code for the last detected type in the WSDL.

Suggested workaround:

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
            'type_ns' => 'http://......lessimportantsotre',
            'from_xml' => function($xml) {
                $data = simplexml_load_string($xml);
    
                return LessImportantStore::fromXml($data);
            },
        ],
    ],
];
```

More information:

- [Code in php-src](https://github.com/php/php-src/blob/php-7.2.10/ext/soap/php_encoding.c#L468)
- [Functional test](../../test/PhproTest/SoapClient/Functional/Encoding/DuplicateTypenamesTest.php)

## Enumerations

It is possible that the WSDL file contains `xsd:enumeration` elements.
Since PHP does not have an internal `enum` type,
ext-soap will transform the data to the type that is determined in the `xsd:restriction` section.
The soap client will never try to validate the input against the configured enumerations inside the WSDL.

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
- Internally, ext-soap will not register any types, since it converts the value to a string.
- You won't be able to access the available options without manually parsing the WSDL file.
- If you do want to use a custom class for the enumerations type, you can create a type converter like this:

```php
$soapOptions = [
    'typemap' => [
        [
            'type_name' => 'PhoneTypeEnum',
            'type_ns' => 'http://soapinterop.org/xsd',
            'from_xml' => function($xml) {
                $doc = new \DOMDocument();
                $doc->loadXML($xml);

                return new PhoneTypeEnum($doc->textContent);
            },
            'to_xml' => function(PhoneTypeEnum $enum) {
                return sprintf('<PhoneTypeEnum>%s</PhoneTypeEnum>', $enum->getValue());
            },
        ],
    ],
];
```

More information:
- [Functional test](../../test/PhproTest/SoapClient/Functional/Encoding/EnumTest.php)
- [Lack of validation in php-src](https://github.com/php/php-src/blob/php-7.2.10/ext/soap/php_encoding.c#L3172-L3200)


## SimpleContent

Ext-soap does support `xsd:simpleContent` types. The implementation is a bit strange.

Example:

```xml
<xsd:complexType name="SimpleContent">
    <xsd:simpleContent>
        <xsd:extension base="integer">
            <xsd:attribute name="country" type="string" />
        </xsd:extension>
    </xsd:simpleContent>
</xsd:complexType>
```

- The `SimpleContent` complexType will be registered as a type.
- Code will be generated for this type.
- The fields are:
  - country : string
  - _ : int
- As you can see, the `_` field is being used as the xml textContent.


More information:
- [Functional test](../../test/PhproTest/SoapClient/Functional/Encoding/SimpleContent.php)

