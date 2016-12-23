<?php

namespace Phpro\SoapClient\Soap\HttpBinding;

use Meng\Soap\HttpBinding\RequestBuilder;
use Meng\Soap\HttpBinding\RequestException;
use Psr\Http\Message\RequestInterface;
use Zend\Diactoros\Stream;

/**
 * Class SoapRequest
 *
 * @package Phpro\SoapClient\Soap\HttpBinding
 */
class SoapRequest
{
    /**
     * @var string
     */
    private $request;

    /**
     * @var string
     */
    private $location;

    /**
     * @var string
     */
    private $action;

    /**
     * @var int
     */
    private $version;

    /**
     * @var int
     */
    private $oneWay;

    /**
     * SoapRequest constructor.
     *
     * @param string $request
     * @param string $location
     * @param string $action
     * @param int    $version
     * @param int    $oneWay
     */
    public function __construct(string $request, string $location, string $action, int $version, int $oneWay = 0)
    {
        $this->request = $request;
        $this->location = $location;
        $this->action = $action;
        $this->version = $version;
        $this->oneWay = $oneWay;
    }

    /**
     * @return string
     */
    public function getRequest(): string
    {
        return $this->request;
    }

    /**
     * @return string
     */
    public function getLocation(): string
    {
        return $this->location;
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @return int
     */
    public function getVersion(): int
    {
        return $this->version;
    }

    /**
     * @return int
     */
    public function getOneWay(): int
    {
        return $this->oneWay;
    }

    /**
     * @return RequestInterface
     * @throws \Meng\Soap\HttpBinding\RequestException
     * @throws \InvalidArgumentException
     */
    public function toPsr7Request(): RequestInterface
    {
        $builder = new RequestBuilder();

        $stream = new Stream('php://temp', 'r+');
        $stream->write($this->getRequest());
        $stream->rewind();

        $this->getVersion() === 1 ? $builder->isSOAP11() : $builder->isSOAP12();
        $builder->setEndpoint($this->getLocation());
        $builder->setSoapAction($this->getAction());
        $builder->setSoapMessage($stream);

        try {
            return $builder->getSoapHttpRequest();
        } catch (RequestException $exception) {
            $stream->close();
            throw $exception;
        }
    }
}
