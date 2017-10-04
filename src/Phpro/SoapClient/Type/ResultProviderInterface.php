<?php

namespace Phpro\SoapClient\Type;

/**
 * Interface ResultProviderInterface
 *
 * This Interface can be used when a result is wrapped in a Response object
 *
 * @package Phpro\SoapClient\Type
 */
interface ResultProviderInterface
{

    /**
     * @return ResultInterface
     */
    public function getResult(): ResultInterface;
}
