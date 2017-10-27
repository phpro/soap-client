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
use DOMElement;
use DOMXPath;
use Psr\Http\Message\StreamInterface;
use Zend\Diactoros\Stream;

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
    public function getRootNamespace()
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
    public function getXmlDocument()
    {
        return $this->xml;
    }

    /**
     * @return DOMElement
     */
    public function getRootElement()
    {
        return $this->xml->documentElement;
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
        $stream = new Stream('php://memory', 'r+');
        $stream->write($this->toString());
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
