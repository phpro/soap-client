<?php

/*
 * This file is part of the Phpro application.
 *
 * Copyright (c) 2015-2017 Phpro
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
    public function getResult();
}
