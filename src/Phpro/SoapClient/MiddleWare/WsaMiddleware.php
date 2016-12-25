<?php

namespace Phpro\SoapClient\MiddleWare;

use Phpro\SoapClient\Xml\SoapXml;
use Psr\Http\Message\RequestInterface;
use RobRichards\XMLSecLibs\XMLSecurityDSig;

/**
 * Class WsaMiddleware
 *
 * @package Phpro\SoapClient\MiddleWare
 */
class WsaMiddleware extends Middleware
{
    const XMLNS_WSA = 'http://schemas.xmlsoap.org/ws/2004/08/addressing';
    const WSA_ADDRESS_ANONYMOUS = 'http://schemas.xmlsoap.org/ws/2004/08/addressing/role/anonymous';

    /**
     * @var string
     */
    private $address;

    /**
     * WsaMiddleware constructor.
     *
     * @param string|null $address
     */
    public function __construct(string $address = self::WSA_ADDRESS_ANONYMOUS)
    {
        $this->address = $address;
    }

    /**
     * {@inheritdoc}
     */
    public function beforeRequest(callable $handler, RequestInterface $request, array $options)
    {
        $xml = SoapXml::fromStream($request->getBody());
        $this->addWsa($xml, $request);
        $request = $request->withBody($xml->toStream());

        return $handler($request, $options);
    }

    /**
     * @param SoapXml          $xml
     * @param RequestInterface $request
     */
    private function addWsa(SoapXml $xml, RequestInterface $request)
    {
        $xml->addEnvelopeNamespace('wsa', self::XMLNS_WSA);
        $wsaHeader = $this->parseAdressingHeader($xml, $request);
        $xml->prependSoapHeader($wsaHeader);
    }

    /**
     * @param SoapXml          $xml
     * @param RequestInterface $request
     *
     * @return \DOMElement
     */
    private function parseAdressingHeader(SoapXml $xml, RequestInterface $request)
    {
        $header = $xml->createSoapHeader();
        $header->appendChild($this->createWsaActionNode($xml, $request));
        $header->appendChild($this->createWsaToNode($xml, $request));
        $header->appendChild($this->createWsaMessageIdNode($xml));
        $header->appendChild($this->createWsaReplyToNode($xml));

        return $header;
    }

    /**
     * @param SoapXml          $xml
     * @param RequestInterface $request
     *
     * @return \DOMElement
     */
    private function createWsaActionNode(SoapXml $xml, RequestInterface $request)
    {
        $document = $xml->getXmlDocument();

        return $document->createElementNS(self::XMLNS_WSA, 'wsa:Action', $request->getHeader('SOAPAction')[0]);
    }

    /**
     * @param SoapXml          $xml
     * @param RequestInterface $request
     *
     * @return \DOMElement
     */
    private function createWsaToNode(SoapXml $xml, RequestInterface $request)
    {
        $document = $xml->getXmlDocument();

        return $document->createElementNS(self::XMLNS_WSA, 'wsa:To', (string) $request->getUri());
    }

    /**
     * @param SoapXml $xml
     *
     * @return \DOMElement
     */
    private function createWsaMessageIdNode(SoapXml $xml)
    {
        $document = $xml->getXmlDocument();
        $messageId = XMLSecurityDSig::generateGUID('uuid:');

        return $document->createElementNS(self::XMLNS_WSA, 'wsa:MessageID', $messageId);
    }

    /**
     * @param SoapXml $xml
     *
     * @return \DOMElement
     */
    public function createWsaReplyToNode(SoapXml $xml)
    {
        $document = $xml->getXmlDocument();
        $address = $this->address ?: self::WSA_ADDRESS_ANONYMOUS;

        $replyNode = $document->createElementNS(self::XMLNS_WSA, 'wsa:ReplyTo');
        $replyNode->appendChild($document->createElementNS(self::XMLNS_WSA, 'wsa:Address', $address));

        return $replyNode;
    }
}
