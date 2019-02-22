<?php

namespace Phpro\SoapClient\Middleware;

use Http\Promise\Promise;
use Phpro\SoapClient\Soap\Handler\LastRequestInfoCollectorInterface;
use Phpro\SoapClient\Soap\HttpBinding\Converter\Psr7ToLastRequestInfoConverter;
use Phpro\SoapClient\Soap\HttpBinding\LastRequestInfo;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class CollectLastRequestInfoMiddleware extends Middleware implements LastRequestInfoCollectorInterface
{
    /**
     * @var RequestInterface|null
     */
    private $lastRequest;

    /**
     * @var ResponseInterface|null
     */
    private $lastResponse;

    public function getName(): string
    {
        return 'collect_last_request_info_middleware';
    }

    public function beforeRequest(callable $handler, RequestInterface $request): Promise
    {
        $this->lastRequest = $request;
        $this->lastResponse = null;

        return $handler($request);
    }

    public function afterResponse(ResponseInterface $response): ResponseInterface
    {
        $this->lastResponse = $response;

        return $response;
    }

    public function collectLastRequestInfo(): LastRequestInfo
    {
        if (!$this->lastRequest || !$this->lastResponse) {
            return LastRequestInfo::createEmpty();
        }

        return (new Psr7ToLastRequestInfoConverter())->convert($this->lastRequest, $this->lastResponse);
    }
}
