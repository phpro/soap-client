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
     * @deprecated use getClientNamespace or getTypeNamespace instead
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
     * @deprecated Use getTypeDestination or getClientDestination instead
     */
    public function getDestination();

    /**
     * @return string
     */
    public function getClientDestination();

    /**
     * @return string
     */
    public function getTypeDestination();

    /**
     * @return RuleSetInterface
     */
    public function getRuleSet();

    /**
     * @return string
     */
    public function getClientNamespace();

    /**
     * @return string
     */
    public function getTypeNamespace();

    /**
     * @return WsdlProviderInterface
     */
    public function getWsdlProvider();
}
