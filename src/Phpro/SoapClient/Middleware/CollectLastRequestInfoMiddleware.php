<?php

namespace Phpro\SoapClient\Middleware;

use GuzzleHttp\Promise\PromiseInterface;
use Phpro\SoapClient\Soap\Handler\LastRequestInfoCollectorInterface;
use Phpro\SoapClient\Soap\HttpBinding\LastRequestInfo;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class CollectLastRequestInfoMiddleware
 *
 * @package Phpro\SoapClient\Middleware
 */
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

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'collect_last_request_info_middleware';
    }

    /**
     * {@inheritdoc}
     */
    public function beforeRequest(callable $handler, RequestInterface $request, array $options): PromiseInterface
    {
        $this->lastRequest = $request;
        $this->lastResponse = null;

        return $handler($request, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function afterResponse(ResponseInterface $response): ResponseInterface
    {
        $this->lastResponse = $response;

        return $response;
    }

    /**
     * @return LastRequestInfo
     */
    public function collectLastRequestInfo(): LastRequestInfo
    {
        if (!$this->lastRequest || !$this->lastResponse) {
            return LastRequestInfo::createEmpty();
        }

        return LastRequestInfo::createFromPsr7RequestAndResponse($this->lastRequest, $this->lastResponse);
    }
}
