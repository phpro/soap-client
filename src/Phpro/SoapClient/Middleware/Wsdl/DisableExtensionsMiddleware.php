<?php

namespace Phpro\SoapClient\Middleware\Wsdl;

use Phpro\SoapClient\Middleware\Middleware;
use Phpro\SoapClient\Xml\WsdlXml;
use Psr\Http\Message\ResponseInterface;

/**
 * Class DisableExtensionsMiddleware
 *
 * @package Phpro\SoapClient\Middleware\Wsdl
 */
class DisableExtensionsMiddleware extends Middleware
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return 'wsdl_disable_extensions';
    }

    /**
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    public function afterResponse(ResponseInterface $response): ResponseInterface
    {
        $xml = WsdlXml::fromStream($response->getBody());

        /** @var \DOMElement $node */
        foreach ($xml->xpath('//wsdl:binding//*[@wsdl:required]') as $node) {
            $node->setAttributeNS($xml->getRootNamespace(), 'wsdl:required', 'false');
        }

        return $response->withBody($xml->toStream());
    }
}
