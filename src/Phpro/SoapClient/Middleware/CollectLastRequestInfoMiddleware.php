<?php

/*
 * This file is part of the Phpro application.
 *
 * Copyright (c) 2015-2017 Phpro
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phpro\SoapClient\Middleware;

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
    public function beforeRequest(callable $handler, RequestInterface $request, array $options)
    {
        $this->lastRequest = $request;
        $this->lastResponse = null;

        return $handler($request, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function afterResponse(ResponseInterface $response)
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
