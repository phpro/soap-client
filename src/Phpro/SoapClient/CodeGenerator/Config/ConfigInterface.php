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
     * @deprecated Use getTypeDestination or getClientDestination instead
     */
    public function getDestination(): string;

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
    public function getRuleSet(): RuleSetInterface;

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
    public function getWsdlProvider(): WsdlProviderInterface;

    /**
     * @return string
     */
    public function getClassMapName() : string;

    /**
     * @return string
     */
    public function getClassMapNamespace() : string;

    /**
     * @return string
     */
    public function getClassMapDestination() : string;
}
