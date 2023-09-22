# Configuration

The code generation commands require a configuration file to determine how the SOAP classes need to be generated.

```php
<?php
// my-soap-config.php

use Phpro\SoapClient\CodeGenerator\Config\Config;
use Phpro\SoapClient\CodeGenerator\Rules;
use Phpro\SoapClient\CodeGenerator\Assembler;
use Phpro\SoapClient\Soap\CodeGeneratorEngineFactory;
use Soap\ExtSoapEngine\ExtSoapOptions;
use Soap\Wsdl\Loader\FlatteningLoader;
use Soap\Wsdl\Loader\StreamWrapperLoader;

return Config::create()
    ->setEngine(CodeGeneratorEngineFactory::create(
        'wsdl.xml',
        new FlatteningLoader(new StreamWrapperLoader())
    ))
    ->setTypeDestination('src/SoapTypes')
    ->setTypeNamespace('SoapTypes')
    ->setClientDestination('src/SoapClient')
    ->setClientNamespace('SoapClient')
    ->setClientName('MySoapClient')
    ->setClassMapNamespace('Acme\\Classmap')
    ->setClassMapDestination('src/acme/classmap')
    ->setClassMapName('AcmeClassmap')
    ->addRule(new Rules\AssembleRule(new Assembler\GetterAssembler(
        (new Assembler\GetterAssemblerOptions())
            ->withReturnType()
            ->withBoolGetters()
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

Luckily a command is provided to generate this for you in an interactive manner.
Execute `vendor/bin/soap-client generate:config` to start the interactive config generator.

**engine**

`Soap\Engine\Engine` - REQUIRED

Specify how the code generation tool can talk to SOAP.
By default, we push a custom engine that deeply parses the WSDL for code generation purpose.
For loading the WSDL, a PSR-18 based WSDL loader is being used in 'flattening' mode.
It is possible to change this to any other configuration you want to use
and provide additional options like the preferred SOAP version.

[Read more about engines.](https://github.com/php-soap/engine)

**type destination**

String - REQUIRED

The destination of the generated PHP classes. 

**client destination**

String - REQUIRED

The destination of the generated soap client. 

**type namespace**

String - OPTIONAL

The namespace of the PHP Classes you want to generate.


**client namespace**

String - OPTIONAL

The namespace of the generated client.

**client name**

String - OPTIONAL

The class name of the client, defaults to 'Client'.

**classmap name**

Name of the classmap class

**classmap destination**

The location of a directory the classmap should be generated in.

**classmap namespace**

Name for the classmap

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
