# Code generation rules

Since every application has it's own rules on how to write and structure code,
we made it possible to configure the way your code is auto generated.
You can specify your own rules and apply them to the code generator.

The goal of a rule is to run a code assembler.
[Here you can find a full list of built-in code assemblers](assemblers.md#built-in-assemblers).

 
# Built-in rules

- [AssemblerRule](#assemblerrule)
- [ClientMethodMatchesRule](#clientmethodmatchesrule)
- [IsRequestRule](#isrequestrule)
- [IsResultRule](#isresultrule)
- [MultiRule](#multirule)
- [PropertynameMatchesRule](#propertynamematchesrule)
- [TypeMapRule](#TypeMapRule)
- [TypenameMatchesRule](#typenamematchesrule)

## AssemblerRule

```php
use Phpro\SoapClient\CodeGenerator\Assembler;
use Phpro\SoapClient\CodeGenerator\Rules;

$rule = Rules\AssembleRule(new Assembler\GetterAssembler($someGetterOptions))
```

The `AssemblerRule` will always apply an assembler in the right context. 
This way, the code is added during every code generation command.

In the example above, a getter will be created for every property in the SOAP type.

## ClientMethodMatchesRule

```php
use My\Project\CodeGenerator\Assembler as CustomAssembler;
use Phpro\SoapClient\CodeGenerator\Rules;

new Rules\ClientMethodMatchesRule(
    new Rules\AssembleRule(new CustomAssembler\RemoveClientMethodAssembler()),
    '/demoSetup$/'
)
```

The `ClientMethodMatchesRule` can be used in the types generation command and contains a subRule and a regular expression.
The subRule is mostly a regular AssembleRule, but can be any class that implements the RuleInterface.
The regular expression will be matched against the method name added to the generated Client. 
If the regular expression matches and the subRule is accepted, the defined assembler will run.
 
In the example above, a custom `RemoveClientMethodAssembler` is is used to remove the `demoSetup` method from the Client completely.

## IsRequestRule

```php
use Phpro\SoapClient\CodeGenerator\Assembler;
use Phpro\SoapClient\CodeGenerator\Rules;
use Phpro\SoapClient\Soap\Engine\Metadata\MetadataInterface;

assert($metadata instanceof MetadataInterface);

new Rules\IsRequestRule(
    $metadata,
    new Rules\AssembleRule(new Assembler\RequestAssembler())
)
```

The `IsRequestRule` can be used in the "types" generation command and contains the engine's metadata and a subRule.
The rule will try to guess all request types based on the provided SOAP metadata.
If the type matches a request type and the subRule is accepted, the defined assembler will run.

This rule can be used to e.g. add the required `RequestInterface` to request objects.


## IsResultRule

```php
use Phpro\SoapClient\CodeGenerator\Assembler;
use Phpro\SoapClient\CodeGenerator\Rules;
use Phpro\SoapClient\Soap\Engine\Metadata\MetadataInterface;

assert($metadata instanceof MetadataInterface);

new Rules\IsResultRule(
    $metadata,
    new Rules\AssembleRule(new Assembler\ResultAssembler())
)
```

The `IsResultRule` can be used in the "types" generation command and contains the engine's metadata and a subRule.
The rule will try to guess all response types based on the provided SOAP metadata.
If the type matches a response type and the subRule is accepted, the defined assembler will run.

This rule can be used to e.g. add the required `ResultInterface` to request objects.


## MultiRule

```php
use Phpro\SoapClient\CodeGenerator\Assembler;
use Phpro\SoapClient\CodeGenerator\Rules;

$rule = Rules\MultiRule([
    Rules\AssembleRule(new Assembler\GetterAssembler($someGetterOptions)),
    Rules\AssembleRule(new Assembler\SetterAssembler()),
]);
```

The `MultiRule` makes it possible to define multiple rules that need to be applied on a SOAP type.
This rule can be very handy in combination with rules like the `TypeMapRule` or `TypenameMatchesRule`.
By using the `MultiRule`, you can e.g. specify the regex once but run multiple assemblers.

In the example above, both the getters and setters are added for every property in the SOAP type.


## PropertynameMatchesRule

```php
use Phpro\SoapClient\CodeGenerator\Assembler;
use Phpro\SoapClient\CodeGenerator\Rules;

new Rules\PropertynameMatchesRule(
    new Rules\AssembleRule(new Assembler\InterfaceAssembler(ApiKeyAwareInterface::class)),
    '/^ApiKey$/'
)
```

The `PropertynameMatchesRule` can be used in the types generation command and contains a subRule and a regular expression.
The subRule is mostly a regular AssmbleRule, but can be any class that implements the RuleInterface.
The regular expression will be matched against the normalized SOAP property name. 
If the regular expression matches and the subRule is accepted, the defined assembler will run.
 
In the example above, the `ApiKeyAwareInterface` is added to the class if the SOAP property `ApiKey` exists.


## TypeMapRule

```php
use Phpro\SoapClient\CodeGenerator\Assembler;
use Phpro\SoapClient\CodeGenerator\Rules;

$resultProviderRule = new Rules\AssembleRule(new Assembler\ResultProviderAssembler());
$defaultRule = new Rules\AssembleRule(new Assembler\ResultAssembler());

new Rules\TypeMapRule(
    [
        'SomeType' => $resultProviderRule,
        'NullType' => null
    ]
    $defaultRule
)
```

The `TypeMapRule` can be used in the types generation command and contains a map of types with a subrule.
When the SOAP type is found in the TypeMap, the configured rule will be applied.
The last parameter is the default rule. When the type cannot be found in the TypeMap, the default Rule will apply.
This rule will make it easy to apply some specific rules for specific templates.
 
In the example above, the `ResultProviderInterface` is added to the class if the SOAP type equals `SomeType`.
On the SOAP type `NullType`, no actions are executed.
On all other SOAP types, the `ResultInterface` is added.


## TypenameMatchesRule

```php
use Phpro\SoapClient\CodeGenerator\Assembler;
use Phpro\SoapClient\CodeGenerator\Rules;

new Rules\TypenameMatchesRule(
    new Rules\AssembleRule(new Assembler\RequestAssembler()),
    '/Request$/'
)
```

The `TypenameMatchesRule` can be used in the types generation command and contains a subRule and a regular expression.
The subRule is mostly a regular AssembleRule, but can be any class that implements the RuleInterface.
The regular expression will be matched against the normalized SOAP type name. 
If the regular expression matches and the subRule is accepted, the defined assembler will run.
 
In the example above, the `RequestInterface` is added to the class if the SOAP type ends on `Request`.


# Creating your own Rule

Creating your own Rule is pretty easy. 
The only thing you'll need to do is implementing the `RuleInterface`.

```php
/**
 * Interface RuleInterface
 *
 * @package Phpro\SoapClient\CodeGenerator\Rules
 */
interface RuleInterface
{
    /**
     * @param ContextInterface $context
     *
     * @return bool
     */
    public function appliesToContext(ContextInterface $context);

    /**
     * @param ContextInterface $context
     */
    public function apply(ContextInterface $context);
}
```

Possible contexts:

- `ClassMapContext`: Triggered during the `generate:classmap` command.
- `TypeContext`: Triggered during the `generate:types` command for every type in the SOAP scheme.
- `PropertyContext`: Triggered during the `generate:types` command for every property in a SOAP type.
