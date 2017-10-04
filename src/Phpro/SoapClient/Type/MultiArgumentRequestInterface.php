<?php

namespace Phpro\SoapClient\Type;

/**
 * Class MultiArgumentRequestInterface
 *
 * @package Phpro\SoapClient\Type\Legacy
 */
interface MultiArgumentRequestInterface extends RequestInterface
{
    /**
     * @return array
     */
    public function getArguments(): array;
}
