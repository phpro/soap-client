# Configuration

The code generation commands require a configuration file to determine how the SOAP classes need to be generated.

```php
<?php
// my-soap-config.php

use Phpro\SoapClient\CodeGenerator\Config\Config;
use Phpro\SoapClient\CodeGenerator\Rules;
use Phpro\SoapClient\CodeGenerator\Assembler;

return Config::create()
    ->setWsdl('http://localhost/path/to/soap.wsdl')
    ->setTypeDestination('src/SoapTypes')
    ->setTypeNamespace('SoapTypes')
    ->setClientDestination('src/SoapClient')
    ->setClientNamespace('SoapClient')
    ->setClientName('MySoapClient')
    ->addSoapOption('features', SOAP_SINGLE_ELEMENT_ARRAYS)
    ->addRule(new Rules\AssembleRule(new Assembler\GetterAssembler(
        (new Assembler\GetterAssemblerOptions())
            ->withReturnType(true)
            ->withBoolGetters(true)
    )))
    ->addRule(new Rules\TypenameMatchesRule(
        new Rules\AssembleRule(new Assembler\RequestAssembler()),
        '/Request$/'
    ))
    ->addRule(new Rules\TypenameMatchesRule(
        new Rules\AssembleRule(new Assembler\ResultAssembler()),
        '/Response$/'
    ))
;
```

**wsdl**

String - REQUIRED

The full path the the WSDL file you want to parse


**type destination**

String - REQUIRED

The destination of the generated PHP classes. 

**client destination**

String - REQUIRED

The destination of the generated soap client. 


**soapOptions**

Array - OPTIONAL

The soap options you want to add to the SoapClient during code generation.
Default values:


```php
[
    'trace' => false,
    'exceptions' => true,
    'keep_alive' => true,
    'cache_wsdl' => WSDL_CACHE_NONE,
]
```


**type namespace**

String - OPTIONAL

The namespace of the PHP Classes you want to generate.


**client namespace**

String - OPTIONAL

The namespace of the generated client.

**client name**

String - OPTIONAL

The class name of the client, defaults to 'Client'.

**rules**

RuleInterface - OPTIONAL

You can specify how you want to generate your code.
More information about the topic is available in the [rules](rules.md) and [assemblers](assemblers.md) section.

The pre-defined rules are override-able by calling `setRuleSet` on the constucted object.

For example, to make all your properties protected:
```php
Config::create()
    ->setRuleSet(
        new Rules\RuleSet(
            [
                new Rules\AssembleRule(new Assembler\PropertyAssembler(PropertyGenerator::VISIBILITY_PROTECTED)),
                new Rules\AssembleRule(new Assembler\ClassMapAssembler()),
            ]
        )
    )
```