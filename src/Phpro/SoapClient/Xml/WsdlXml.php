<?php

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
    public function getWsdlNamespaceUri(): string
    {
        return $this->getRootNamespace();
    }
}
