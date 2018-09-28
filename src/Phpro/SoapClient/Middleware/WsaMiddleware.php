<?php

namespace Phpro\SoapClient\Middleware;

use Http\Promise\Promise;
use Phpro\SoapClient\Exception\RequestException;
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
        $wsa->addAction($this->detectSoapAction($request));
        $wsa->addTo((string) $request->getUri());
        $wsa->addMessageID();
        $wsa->addReplyTo($this->address);

        $request = $request->withBody($xml->toStream());

        return $handler($request);
    }

    private function detectSoapAction(RequestInterface $request): string
    {
        $header = $request->getHeader('SOAPAction');
        if ($header) {
            return $header[0];
        }

        $contentType = $request->getHeader('Content-Type')[0];
        foreach (explode(';', $contentType) as $part) {
            if (strpos($part, 'action=') !== false) {
                return trim(explode('=', $part)[1], '"\'');
            }
        }

        throw new RequestException('Action not found in HTTP headers to be included in WSA headers.');
    }
}
