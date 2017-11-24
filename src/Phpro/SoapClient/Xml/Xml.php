<?php

namespace Phpro\SoapClient\Xml;

use DOMDocument;
use DOMElement;
use DOMXPath;
use Http\Discovery\StreamFactoryDiscovery;
use Psr\Http\Message\StreamInterface;

/**
 * Class Xml
 *
 * @package Phpro\SoapClient\Xml
 */
class Xml
{
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
    }

    /**
     * @return string
     */
    public function getRootNamespace(): string
    {
        return $this->getRootElement()->namespaceURI;
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
     * @return DOMDocument
     */
    public function getXmlDocument(): DOMDocument
    {
        return $this->xml;
    }

    /**
     * @return DOMElement
     */
    public function getRootElement(): DOMElement
    {
        return $this->xml->documentElement;
    }

    /**
     * @param string $expression
     *
     * @return \DOMNodeList
     */
    public function xpath(string $expression): \DOMNodeList
    {
        return $this->xpath->query($expression);
    }

    /**
     * @param StreamInterface $stream
     *
     * @return Xml
     * @throws \RuntimeException
     */
    public static function fromStream(StreamInterface $stream): Xml
    {
        $xml = new DOMDocument();
        $xml->loadXML($stream->getContents());

        return new static($xml);
    }

    /**
     * @param string $content
     *
     * @return Xml
     */
    public static function fromString(string $content): Xml
    {
        $xml = new DOMDocument();
        $xml->loadXML($content);

        return new static($xml);
    }

    /**
     * @return StreamInterface
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function toStream(): StreamInterface
    {
        $streamFactory = StreamFactoryDiscovery::find();
        $stream = $streamFactory->createStream($this->toString());
        $stream->rewind();

        return $stream;
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->xml->saveXML();
    }
}
