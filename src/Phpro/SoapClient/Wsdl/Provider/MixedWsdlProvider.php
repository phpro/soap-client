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

/**
 * Class MixedWsdlProvider
 *
 * @package Phpro\SoapClient\Wsdl\Provider
 */
class MixedWsdlProvider implements WsdlProviderInterface
{
    /**
     * This provider passes the user input directly as output.
     * It will let the PHP Soap-client handle errors.
     *
     * {@inheritdoc}
     */
    public function provide(string $source)
    {
        return $source;
    }
}
