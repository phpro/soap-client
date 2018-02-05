<?php

namespace Phpro\SoapClient\Middleware;

use Http\Promise\Promise;
use Phpro\SoapClient\Xml\SoapXml;
use Psr\Http\Message\RequestInterface;
use RobRichards\WsePhp\WSASoap;

class WsaMiddleware extends Middleware
{
    const WSA_ADDRESS_ANONYMOUS = 'http://schemas.xmlsoap.org/ws/2004/08/addressing/role/anonymous';

    private $address;

    public function __construct(string $address = self::WSA_ADDRESS_ANONYMOUS)
    {
        $this->address = $address;
    }

    public function getName(): string
    {
        return 'wsa_middleware';
    }

    public function beforeRequest(callable $handler, RequestInterface $request): Promise
    {
        $xml = SoapXml::fromStream($request->getBody());

        $wsa = new WSASoap($xml->getXmlDocument());

        $actionHeader = $request->getHeader('SOAPAction');
        if ($actionHeader) {
            $wsa->addAction($actionHeader[0]);
        } else {
            $contentType = $request->getHeader('Content-Type')[0];
            $parts = explode(';', $contentType);
            foreach ($parts as $part) {
                if (strpos($part, 'action=') !== false) {
                    $actionParts = explode('=', $part);
                    $action = trim($actionParts[1], '"');
                    $wsa->addAction($action);
                }
            }
        }
        
        $wsa->addTo((string) $request->getUri());
        $wsa->addMessageID();
        $wsa->addReplyTo($this->address);

        $request = $request->withBody($xml->toStream());

        return $handler($request);
    }
}
