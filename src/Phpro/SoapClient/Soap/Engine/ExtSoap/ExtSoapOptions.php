<?php

declare(strict_types=1);

namespace Phpro\SoapClient\Soap\Engine\ExtSoap;

class ExtSoapOptions
{
    /**
     * @var string
     */
    private $wsdl;

    /**
     * @var array
     */
    private $options;

    public function __construct(string $wsdl, array $options)
    {
        $this->wsdl = $wsdl;
        $this->options = $options;
    }

    /**
     * @return string
     */
    public function getWsdl(): string
    {
        return $this->wsdl;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}
