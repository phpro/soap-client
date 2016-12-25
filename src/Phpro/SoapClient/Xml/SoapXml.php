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
class SoapXml
{
    const XMLNS_XMLNS = 'http://www.w3.org/2000/xmlns/';

    /**
     * @var DOMDocument
     */
    private $xml;

    /**
     * @var DOMXPath
     */
    private $xpath;

    /**
     * SoapXml constructor.
     *
     * @param DOMDocument $xml
     */
    public function __construct(DOMDocument $xml)
    {
        $this->xml = $xml;
        $this->xpath = new DOMXPath($xml);

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
    public function registerNamespace(string $prefix, string $namespaceUri)
    {
        $this->xpath->registerNamespace($prefix, $namespaceUri);
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
     * @return DOMDocument
     */
    public function getXmlDocument()
    {
        return $this->xml;
    }

    /**
     * @return DOMElement
     */
    public function getEnvelope()
    {
        return $this->xml->documentElement;
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
        return $this->xml->createElementNS($this->getSoapNamespaceUri(), 'soap:Header');
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

    /**
     * @param $expression
     *
     * @return \DOMNodeList
     */
    public function xpath($expression)
    {
        return $this->xpath->query($expression);
    }
    
    /**
     * @param StreamInterface $stream
     *
     * @return SoapXml
     * @throws \RuntimeException
     */
    public static function fromStream(StreamInterface $stream): SoapXml
    {
        $xml = new DOMDocument();
        $xml->loadXML($stream->getContents());

        return new self($xml);
    }

    /**
     * @return StreamInterface
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function toStream(): StreamInterface
    {
        $stream = new Stream('php://memory', 'r+');
        $stream->write($this->xml->saveXML());
        $stream->rewind();

        return $stream;
    }
}
