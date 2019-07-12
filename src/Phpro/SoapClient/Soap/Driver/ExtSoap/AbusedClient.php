<?php

declare(strict_types=1);

namespace Phpro\SoapClient\Soap\Driver\ExtSoap;

use Phpro\SoapClient\Soap\HttpBinding\SoapRequest;
use Phpro\SoapClient\Soap\HttpBinding\SoapResponse;

class AbusedClient extends \SoapClient
{
    /**
     * @var SoapRequest|null
     */
    protected $storedRequest;

    /**
     * @var SoapResponse|null
     */
    protected $storedResponse;

    public function __construct($wsdl, array $options = [])
    {
        $options = ExtSoapOptionsResolverFactory::createForWsdl($wsdl)->resolve($options);
        parent::__construct($wsdl, $options);
    }

    public static function createFromOptions(ExtSoapOptions $options): self
    {
        return new self($options->getWsdl(), $options->getOptions());
    }

    public function __doRequest($request, $location, $action, $version, $oneWay = 0)
    {
        $this->storedRequest = new SoapRequest($request, $location, $action, $version, $oneWay);

        return $this->storedResponse ? $this->storedResponse->getResponse() : '';
    }

    public function doActualRequest(
        string $request,
        string $location,
        string $action,
        int $version,
        int $oneWay = 0
    ): string {
        return (string) parent::__doRequest($request, $location, $action, $version, $oneWay);
    }

    public function collectRequest(): SoapRequest
    {
        if (!$this->storedRequest) {
            throw new \RuntimeException('No request has been registered yet.');
        }

        return $this->storedRequest;
    }

    public function registerResponse(SoapResponse $response)
    {
        $this->storedResponse = $response;
    }

    public function cleanUpTemporaryState()
    {
        $this->storedRequest = null;
        $this->storedResponse = null;
    }
}
