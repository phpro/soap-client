<?php

/*
 * This file is part of the Phpro application.
 *
 * Copyright (c) 2015-2017 Phpro
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Phpro\SoapClient\CodeGenerator\Util;

use Phpro\SoapClient\CodeGenerator\Util\Normalizer;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

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
        $this->normalizeClassname('my-./*type_123')->shouldReturn('Mytype_123');
    }

    function it_noramizes_properties()
    {
        $this->normalizeProperty('prop1')->shouldReturn('prop1');
        $this->normalizeProperty('my-./*prop_123')->shouldReturn('myprop_123');
    }

    function it_normalizes_datatypes()
    {
        $this->normalizeDataType('string')->shouldReturn('string');
        $this->normalizeDataType('stdClass')->shouldReturn('stdClass');
        $this->normalizeDataType('Iterator')->shouldReturn('Iterator');
        $this->normalizeDataType('long')->shouldReturn('int');
        $this->normalizeDataType('short')->shouldReturn('int');
        $this->normalizeDataType('dateTime')->shouldReturn('\\DateTime');
        $this->normalizeDataType('date')->shouldReturn('\\DateTime');
        $this->normalizeDataType('boolean')->shouldReturn('bool');
        $this->normalizeDataType('decimal')->shouldReturn('float');
    }

    function it_generates_property_methods()
    {
        $this->generatePropertyMethod('get', 'prop1')->shouldReturn('getProp1');
        $this->generatePropertyMethod('set', 'prop1')->shouldReturn('setProp1');
        $this->generatePropertyMethod('get', 'prop1_test*./')->shouldReturn('getProp1_test');
    }

    function it_gets_classname_from_fqn()
    {
        $this->getClassNameFromFQN('MyClass')->shouldReturn('MyClass');
        $this->getClassNameFromFQN('\Namespace\MyClass')->shouldReturn('MyClass');
        $this->getClassNameFromFQN('Vendor\Namespace\MyClass')->shouldReturn('MyClass');
    }

    function it_gets_complete_use_statement()
    {
        $this->getCompleteUseStatement('Namespace\MyClass', 'ClassAlias')->shouldReturn('Namespace\MyClass as ClassAlias');
        $this->getCompleteUseStatement('Namespace\MyClass', null)->shouldReturn('Namespace\MyClass');
        $this->getCompleteUseStatement('MyClass', '')->shouldReturn('MyClass');
    }
}
