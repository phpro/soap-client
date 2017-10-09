<?php

/*
 * This file is part of the Phpro application.
 *
 * Copyright (c) 2015-2017 Phpro
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phpro\SoapClient\Xml;

use DOMDocument;

/**
 * Class WsdlXml
 *
 * @package Phpro\SoapClient\Xml
 */
class WsdlXml extends Xml
{
    /**
     * SoapXml constructor.
     *
     * @param DOMDocument $xml
     */
    public function __construct(DOMDocument $xml)
    {
        parent::__construct($xml);

        // Register some default namespaces for easy access:
        $this->registerNamespace('wsdl', $this->getWsdlNamespaceUri());
    }

    /**
     * @return string
     */
    public function getWsdlNamespaceUri()
    {
        return $this->getRootNamespace();
    }
}
