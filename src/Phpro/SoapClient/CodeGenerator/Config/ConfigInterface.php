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
    public function getNamespace();

    /**
     * @return string
     */
    public function getWsdl();

    /**
     * array
     */
    public function getSoapOptions();

    /**
     * @return string
     */
    public function getDestination();

    /**
     * @return RuleSetInterface
     */
    public function getRuleSet();

    /**
     * @return WsdlProviderInterface
     */
    public function getWsdlProvider();
}
