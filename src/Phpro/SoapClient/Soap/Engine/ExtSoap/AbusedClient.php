<?php

declare(strict_types=1);

namespace Phpro\SoapClient\Soap\Engine\ExtSoap;

use Phpro\SoapClient\Soap\HttpBinding\SoapRequest;

class AbusedClient extends \SoapClient
{
    /**
     * @var SoapRequest
     */
    private $request;

    /**
     * @var string
     */
    private $response = '';

    public static function createFromOptions(ExtSoapOptions $options): self
    {
        return new self($options->getWsdl(), $options->getOptions());
    }

    public function __doRequest($request, $location, $action, $version, $oneWay = 0)
    {
        $this->request = new SoapRequest($request, $location, $action, $version, $oneWay);

        return $this->response;
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
    public function doInternalRequest(
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
        if (!$this->request) {
            throw new \RuntimeException('No request has been registered yet.');
        }

        return $this->request;
    }

    public function registerResponse(string $response)
    {
        $this->response = $response;
    }
}
