<?php

namespace Phpro\SoapClient\Middleware;

use GuzzleHttp\Promise\PromiseInterface;
use Phpro\SoapClient\Xml\SoapXml;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class RemoveEmptyNodesMiddleware extends MiddleWare
{

    public function getName(): string
    {
        return 'remove_empty_nodes_middleware';
    }

    /**
     * @param callable         $handler
     * @param RequestInterface $request
     * @param array            $options
     *
     * @return PromiseInterface
     */
    public function beforeRequest(callable $handler, RequestInterface $request, array $options)
    {
        $xml = SoapXml::fromStream($request->getBody());

        // remove all empty nodes
        while (($notNodes = $xml->xpath('//*[not(node())]')) && ($notNodes->length)) {
            foreach ($notNodes as $node) {
                $node->parentNode->removeChild($node);
            }
        }

        $request = $request->withBody($xml->toStream());

        return $handler($request, $options);
    }

    /**
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    public function afterResponse(ResponseInterface $response)
    {
        return $response;
    }
}
