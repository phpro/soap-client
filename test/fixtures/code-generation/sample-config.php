<?php

use Phpro\SoapClient\CodeGenerator\Config\Config;
use Phpro\SoapClient\CodeGenerator\Rules;
use Phpro\SoapClient\CodeGenerator\Assembler;

return Config::create()
    ->setWsdl('http://wsf.cdyne.com/WeatherWS/Weather.asmx?WSDL')
    ->setTypeDestination('codegen')
    ->setTypeNamespace('Testing')
    ->addRule(new Rules\AssembleRule(new Assembler\GetterAssembler(GetterAssemblerOptions::create()->withBoolGetters(false))))
    ->addRule(new Rules\TypenameMatchesRule(
        new Rules\AssembleRule(new Assembler\ResultAssembler()),
        '/Response$/'
    ))
;
