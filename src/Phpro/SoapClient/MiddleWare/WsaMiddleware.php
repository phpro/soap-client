<?php

namespace Phpro\SoapClient\MiddleWare;

use Phpro\SoapClient\Xml\SoapXml;
use Psr\Http\Message\RequestInterface;
use RobRichards\WsePhp\WSASoap;

/**
 * Class WsaMiddleware
 *
 * @package Phpro\SoapClient\MiddleWare
 */
class WsaMiddleware extends Middleware
{
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
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function beforeRequest(callable $handler, RequestInterface $request, array $options)
    {
        $xml = SoapXml::fromStream($request->getBody());

        $wsa = new WSASoap($xml->getXmlDocument());
        $wsa->addAction($request->getHeader('SOAPAction')[0]);
        $wsa->addTo((string) $request->getUri());
        $wsa->addMessageID();
        $wsa->addAction($this->address);

        $request = $request->withBody($xml->toStream());

        return $handler($request, $options);
    }
}
