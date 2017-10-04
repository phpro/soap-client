<?php

namespace Phpro\SoapClient\CodeGenerator\Config;

use Phpro\SoapClient\CodeGenerator\Rules\RuleSetInterface;
use Phpro\SoapClient\Wsdl\Provider\WsdlProviderInterface;

/**
 * Interface ConfigInterface
 *
 * @package Phpro\SoapClient\CodeGenerator\Config
 */
interface ConfigInterface
{
    /**
     * @return string
     */
    public function getNamespace(): string;

    /**
     * @return string
     */
    public function getWsdl(): string;

    /**
     * array
     */
    public function getSoapOptions(): array;

    /**
     * @return string
     */
    public function getDestination(): string;

    /**
     * @return RuleSetInterface
     */
    public function getRuleSet(): RuleSetInterface;

    /**
     * @return WsdlProviderInterface
     */
    public function getWsdlProvider(): WsdlProviderInterface;
}
