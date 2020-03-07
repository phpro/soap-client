<?php

namespace Phpro\SoapClient\Middleware;

use Http\Promise\Promise;
use Phpro\SoapClient\Xml\SoapXml;
use Phpro\SoapClient\Xml\Xml;
use Psr\Http\Message\RequestInterface;

class RemoveEmptyNodesMiddleware extends Middleware
{
    public function getName(): string
    {
        return 'remove_empty_nodes_middleware';
    }

    public function beforeRequest(callable $handler, RequestInterface $request): Promise
    {
        $xml = SoapXml::fromStream($request->getBody());

        // remove all empty nodes
        while ($notNodes = $this->getNotNodes($xml)) {
            foreach ($notNodes as $node) {
                $node->parentNode->removeChild($node);
            }
        }

        $request = $request->withBody($xml->toStream());

        return $handler($request);
    }

    private function getNotNodes(Xml $xml): ?\DOMNodeList
    {
        $notNodes = $xml->xpath('//soap:Envelope/*//*[not(node())]');
        if (!$notNodes->length) {
            return null;
        }

        return $notNodes;
    }
}
