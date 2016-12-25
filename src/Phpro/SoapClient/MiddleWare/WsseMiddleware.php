<?php

namespace Phpro\SoapClient\MiddleWare;

use Phpro\SoapClient\Xml\SoapXml;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RobRichards\WsePhp\WSSESoap;

/**
 * Class WsseMiddleware
 *
 * @package Phpro\SoapClient\MiddleWare
 */
class WsseMiddleware extends Middleware
{
    /**
     * @var callable
     */
    private $wssePrepare;

    /**
     * @var callable
     */
    private $wsseResolve;

    /**
     * WsseMiddleware constructor.
     */
    public function __construct(callable $wssePrepare, callable $wsseResolve)
    {
        $this->wssePrepare = $wssePrepare;
        $this->wsseResolve = $wsseResolve;
    }

    /**
     * {@inheritdoc}
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function beforeRequest(callable $handler, RequestInterface $request, array $options)
    {
        $xml = SoapXml::fromStream($request->getBody()->getContents());

        $wsse = new WSSESoap($xml->getXmlDocument());
        $this->wssePrepare($wsse);

        $request = $request->withBody($xml->toStream());

        return $handler($request, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function afterResponse(ResponseInterface $response)
    {
        $xml = SoapXml::fromStream($response->getBody()->getContents());

        $wsse = new WSSESoap($xml->getXmlDocument());
        $this->wsseResolve($wsse);

        return $response->withBody($xml->toStream());
    }
}
