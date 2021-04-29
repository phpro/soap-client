<?php

namespace Phpro\SoapClient\Middleware;

use Http\Promise\Promise;
use Phpro\SoapClient\Soap\HttpBinding\Detector\SoapActionDetector;
use Phpro\SoapClient\Xml\SoapXml;
use Psr\Http\Message\RequestInterface;
use RobRichards\WsePhp\WSASoap;

class WsaMiddleware2005 extends Middleware
{
    const WSA_ADDRESS2005_ANONYMOUS = 'http://www.w3.org/2005/08/addressing/anonymous';

    private $address;

    public function __construct(string $address = self::WSA_ADDRESS2005_ANONYMOUS)
    {
        $this->address = $address;
    }

    public function getName(): string
    {
        return 'wsa2005_middleware';
    }

    public function beforeRequest(callable $handler, RequestInterface $request): Promise
    {
        $xml = SoapXml::fromStream($request->getBody());

        $wsa = new WSASoap($xml->getXmlDocument(), WSASoap::WSANS_2005);
        $wsa->addAction(SoapActionDetector::detectFromRequest($request));
        $wsa->addTo((string) $request->getUri());
        $wsa->addMessageID();
        $wsa->addReplyTo($this->address);

        $request = $request->withBody($xml->toStream());

        return $handler($request);
    }
}
