# Known issues in ext-soap

- [Base64 binary](#base64-binary)
- [Duplicate typenames](#duplicate-typenames)
- [Enumerations](#enumerations)
- [SimpleContent](#simplecontent)

Isn't your issue listed below? Feel free to provide additional issues in a functional test.


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
This package will generate the code for the both of them if they are defined as DuplicateType.
Those 2 types can be properly attached to his namespace only if namespace is explicitly defined based on properties names.

Suggested workaround:
```php
<?php
// my-soap-config.php

use Phpro\SoapClient\CodeGenerator\Config\Config;

return Config::create()
    ->setWsdl('http://localhost/path/to/soap.wsdl')
    /* OTHER OPTIONS ... */
    ->setDuplicateTypes([
        new \Phpro\SoapClient\CodeGenerator\Model\DuplicateType('MySpecialType', 'http://localhost/path/to/Datatypes/Namespace1', [
            'Attr1',
            'Attr2'
        ]),
        new \Phpro\SoapClient\CodeGenerator\Model\DuplicateType('MySpecialType', 'http://localhost/path/to/Datatypes/Namespace2', [
            'Attr1',
            'Attr2',
            'AnotherSpecialAttribute'
        ])
    ]);
;
```

This configuration create proper class maps and define typemap option for SoapClient inside generated ClientFactory.
Namespace is automatically suffixed in generated class. After generation must be applied proper namespaces for type hints by hand.

Unfortunately you must manually parse recieved XML from response inside static method **fromXml** which is generated in every duplicate type.
This method use SimpleXML by default but you can change it by your own.

If same type exists in both namespace with exact same properties than only one class will be generated. 
It's up to user to create another class for proper usage.

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
- [Functional test](../../test/PhproTest/SoapClient/Functional/Encoding/SimpleContentTest.php)

