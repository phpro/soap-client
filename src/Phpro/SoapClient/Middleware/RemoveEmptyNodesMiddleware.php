<?php

namespace Phpro\SoapClient\Middleware;

use Http\Promise\Promise;
use Phpro\SoapClient\Xml\SoapXml;
use Psr\Http\Message\RequestInterface;

class RemoveEmptyNodesMiddleware extends MiddleWare
{
    public function getName(): string
    {
        return 'remove_empty_nodes_middleware';
    }

    public function beforeRequest(callable $handler, RequestInterface $request): Promise
    {
        $xml = SoapXml::fromStream($request->getBody());

        // remove all empty nodes
        while (($notNodes = $xml->xpath('//*[not(node())]')) && ($notNodes->length)) {
            foreach ($notNodes as $node) {
                $node->parentNode->removeChild($node);
            }
        }

        $request = $request->withBody($xml->toStream());

        return $handler($request);
    }
}
