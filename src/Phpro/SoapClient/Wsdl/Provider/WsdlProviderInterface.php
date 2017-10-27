<?php

/*
 * This file is part of the Phpro application.
 *
 * Copyright (c) 2015-2017 Phpro
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phpro\SoapClient\Wsdl\Provider;

use Phpro\SoapClient\Exception\WsdlException;

/**
 * Interface WsdlProviderInterface
 *
 * @package Phpro\SoapClient\Wsdl\Provider
 */
interface WsdlProviderInterface
{
    /**
     * The provider uses a source path and will return a target path that can be used by the soap-client.
     *
     * @param string $source
     *
     * @return string
     * @throws WsdlException
     */
    public function provide(string $source);
}
