<?php

namespace spec\Phpro\SoapClient\CodeGenerator\Util;

use Phpro\SoapClient\CodeGenerator\Util\Normalizer;
use PhpSpec\ObjectBehavior;

/**
 * Class NormalizerSpec
 *
 * @package spec\Phpro\SoapClient\CodeGenerator\Util
 * @mixin Normalizer
 */
class NormalizerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Normalizer::class);
    }

    function it_can_normalize_namespace()
    {
        $this->normalizeNamespace('\\NameSpace')->shouldReturn('NameSpace');
        $this->normalizeNamespace('NameSpace\\')->shouldReturn('NameSpace');
        $this->normalizeNamespace('\\NameSpace\\')->shouldReturn('NameSpace');
        $this->normalizeNamespace('Name/Space')->shouldReturn('Name\\Space');
    }

    function it_can_normalize_classnames()
    {
        $this->normalizeClassname('myType')->shouldReturn('MyType');
        $this->normalizeClassname('final')->shouldReturn('FinalType');
        $this->normalizeClassname('Final')->shouldReturn('FinalType');
        $this->normalizeClassname('UpperCased')->shouldReturn('UpperCased');
        $this->normalizeClassname('my-./*type_123')->shouldReturn('MyType123');
        $this->normalizeClassname('my-./final*type_123')->shouldReturn('MyFinalType123');
    }

    function it_can_normalize_fqn_classnames()
    {
        $this->normalizeClassnameInFQN('ns1\\myType')->shouldReturn('ns1\\MyType');
        $this->normalizeClassnameInFQN('ns1\\final')->shouldReturn('ns1\\FinalType');
        $this->normalizeClassnameInFQN('ns1\\Final')->shouldReturn('ns1\\FinalType');
        $this->normalizeClassnameInFQN('ns1\\UpperCased')->shouldReturn('ns1\\UpperCased');
        $this->normalizeClassnameInFQN('ns1\\my-./*type_123')->shouldReturn('ns1\\MyType123');
        $this->normalizeClassnameInFQN('ns1\\my-./final*type_123')->shouldReturn('ns1\\MyFinalType123');

        $this->normalizeClassnameInFQN('string')->shouldReturn('string');
        $this->normalizeClassnameInFQN('NoNamespace')->shouldReturn('NoNamespace');
    }

    function it_can_normalize_method_names()
    {
        $this->normalizeMethodName('myMethod')->shouldReturn('myMethod');
        $this->normalizeMethodName('final')->shouldReturn('finalCall');
        $this->normalizeMethodName('Final')->shouldReturn('finalCall');
        $this->normalizeMethodName('UpperCased')->shouldReturn('upperCased');
        $this->normalizeMethodName('my-./*method_123')->shouldReturn('myMethod_123');
        $this->normalizeMethodName('123hello')->shouldReturn('hello123');
        $this->normalizeMethodName('123final')->shouldReturn('final123');
        $this->normalizeMethodName('123')->shouldReturn('call123');
    }

    function it_noramizes_properties()
    {
        $this->normalizeProperty('prop1')->shouldReturn('prop1');
        $this->normalizeProperty('final')->shouldReturn('final');
        $this->normalizeProperty('Final')->shouldReturn('Final');
        $this->normalizeProperty('UpperCased')->shouldReturn('UpperCased');
        $this->normalizeProperty('my-./*prop_123')->shouldReturn('myProp_123');
        $this->normalizeProperty('My-./*prop_123')->shouldReturn('MyProp_123');
        $this->normalizeProperty('My-./final*prop_123')->shouldReturn('MyFinalProp_123');
    }

    function it_normalizes_datatypes()
    {
        $this->normalizeDataType('string')->shouldReturn('string');
        $this->normalizeDataType('stdClass')->shouldReturn('stdClass');
        $this->normalizeDataType('Iterator')->shouldReturn('Iterator');
        $this->normalizeDataType('long')->shouldReturn('int');
        $this->normalizeDataType('short')->shouldReturn('int');
        $this->normalizeDataType('dateTime')->shouldReturn('\\DateTimeInterface');
        $this->normalizeDataType('date')->shouldReturn('\\DateTimeInterface');
        $this->normalizeDataType('boolean')->shouldReturn('bool');
        $this->normalizeDataType('decimal')->shouldReturn('float');

        // Special cases:
        $this->normalizeDataType('DATE')->shouldReturn('\\DateTimeInterface');
        $this->normalizeDataType('SomeCustomDateType')->shouldReturn('SomeCustomDateType');
        $this->normalizeDataType('ArrayOfConsolidatedAgreement')->shouldReturn('ArrayOfConsolidatedAgreement');
    }

    function it_generates_property_methods()
    {
        $this->generatePropertyMethod('get', 'prop1')->shouldReturn('getProp1');
        $this->generatePropertyMethod('set', 'prop1')->shouldReturn('setProp1');
        $this->generatePropertyMethod('get', 'prop1_test*./')->shouldReturn('getProp1_test');
        $this->generatePropertyMethod('get', 'UpperCased')->shouldReturn('getUpperCased');
        $this->generatePropertyMethod('get', 'my-./*prop_123')->shouldReturn('getMyProp_123');
        $this->generatePropertyMethod('get', 'My-./*prop_123')->shouldReturn('getMyProp_123');
        $this->generatePropertyMethod('get', 'My-./final*prop_123')->shouldReturn('getMyFinalProp_123');
        $this->generatePropertyMethod('get', 'final')->shouldReturn('getFinal');
        $this->generatePropertyMethod('set', 'Final')->shouldReturn('setFinal');
    }

    function it_gets_classname_from_fqn()
    {
        $this->getClassNameFromFQN('MyClass')->shouldReturn('MyClass');
        $this->getClassNameFromFQN('\Namespace\MyClass')->shouldReturn('MyClass');
        $this->getClassNameFromFQN('Vendor\Namespace\MyClass')->shouldReturn('MyClass');
    }

    function it_gets_complete_use_statement()
    {
        $this->getCompleteUseStatement('Namespace\MyClass',
            'ClassAlias')->shouldReturn('Namespace\MyClass as ClassAlias');
        $this->getCompleteUseStatement('Namespace\MyClass', null)->shouldReturn('Namespace\MyClass');
        $this->getCompleteUseStatement('MyClass', '')->shouldReturn('MyClass');
    }
}
