<?php

namespace Phpro\SoapClient\CodeGenerator\Config;

use Phpro\SoapClient\CodeGenerator\Rules\RuleSetInterface;
use Phpro\SoapClient\Soap\Engine\EngineInterface;

/**
 * Interface ConfigInterface
 *
 * @package Phpro\SoapClient\CodeGenerator\Config
 */
interface ConfigInterface
{

    /**
     * @return EngineInterface
     */
    public function getEngine(): EngineInterface;

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

    /**
     * @return string
     */
    public function getClientName():string;
}
