<?php

use Phpro\SoapClient\CodeGenerator\Config\Config;
use Phpro\SoapClient\CodeGenerator\Rules;
use Phpro\SoapClient\CodeGenerator\Assembler;

return Config::create()
    ->setWsdl('http://wsf.cdyne.com/WeatherWS/Weather.asmx?WSDL')
    ->setDestination('codegen')
    ->setNamespace('Testing')
    ->addRule(new Rules\AssembleRule(new Assembler\GetterAssembler()))
    ->addRule(new Rules\TypenameMatchesRule(
        new Rules\AssembleRule(new Assembler\ResultAssembler()),
        '/Response$/'
    ))
;
