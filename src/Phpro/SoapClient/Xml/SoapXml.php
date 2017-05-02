<?php

namespace Phpro\SoapClient\Xml;

use DOMDocument;
use DOMElement;
use DOMXPath;
use Psr\Http\Message\StreamInterface;
use Zend\Diactoros\Stream;

/**
 * Class SoapXml
 *
 * @package Phpro\SoapClient\Xml
 */
class SoapXml extends Xml
{
    const XMLNS_XMLNS = 'http://www.w3.org/2000/xmlns/';

    /**
     * SoapXml constructor.
     *
     * @param DOMDocument $xml
     */
    public function __construct(DOMDocument $xml)
    {
        parent::__construct($xml);

        // Register some default namespaces for easy access:
        $this->registerNamespace('soap', $this->getSoapNamespaceUri());
    }

    /**
     * @return string
     */
    public function getSoapNamespaceUri()
    {
        return $this->getEnvelope()->namespaceURI;
    }

    /**
     * @param string $prefix
     * @param string $namespaceUri
     */
    public function addEnvelopeNamespace(string $prefix, string $namespaceUri)
    {
        $this->getEnvelope()->setAttributeNS(self::XMLNS_XMLNS, sprintf('xmlns:%s', $prefix), $namespaceUri);
        $this->registerNamespace($prefix, $namespaceUri);
    }

    /**
     * @return DOMElement
     */
    public function getEnvelope()
    {
        return $this->getRootElement();
    }

    /**
     * @return \DOMNodeList
     */
    public function getHeaders()
    {
        return $this->xpath('//soap:Envelope/soap:Header');
    }

    /**
     * @return DOMElement
     */
    public function createSoapHeader(): DOMElement
    {
        return $this->getXmlDocument()->createElementNS($this->getSoapNamespaceUri(), 'soap:Header');
    }

    /**
     * @param DOMElement $header
     */
    public function prependSoapHeader(DOMElement $header)
    {
        $envelope = $this->getEnvelope();
        $envelope->insertBefore($header, $envelope->firstChild);
    }

    /**
     * @return DOMElement|null
     */
    public function getBody()
    {
        $list = $this->xpath('//soap:Envelope/soap:Body');

        return $list->length ? $list->item(0) : null;
    }
}
