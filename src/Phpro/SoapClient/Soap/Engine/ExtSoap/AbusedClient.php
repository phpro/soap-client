<?php

declare(strict_types=1);

namespace Phpro\SoapClient\Soap\Engine\ExtSoap;

use Phpro\SoapClient\Soap\HttpBinding\SoapRequest;

class AbusedClient extends \SoapClient
{
    /**
     * @var SoapRequest
     */
    protected $storedRequest;

    /**
     * @var string
     */
    protected $storedResponse = '';

    public static function createFromOptions(ExtSoapOptions $options): self
    {
        return new self($options->getWsdl(), $options->getOptions());
    }

    public function __doRequest($request, $location, $action, $version, $oneWay = 0)
    {
        $this->storedRequest = new SoapRequest($request, $location, $action, $version, $oneWay);

        return $this->storedResponse;
    }

    /**
     * @param string $request
     * @param string $location
     * @param string $action
     * @param int    $version
     * @param int    $oneWay
     *
     * @return string
     */
    public function doActualRequest(
        string $request,
        string $location,
        string $action,
        int $version,
        int $oneWay = 0
    ): string {
        return (string)parent::__doRequest($request, $location, $action, $version, $oneWay);
    }

    /**
     * @return SoapRequest|null
     */
    public function collectRequest(): SoapRequest
    {
        if (!$this->storedRequest) {
            throw new \RuntimeException('No request has been registered yet.');
        }

        return $this->storedRequest;
    }

    public function registerResponse(string $response)
    {
        $this->storedResponse = $response;
    }
}
