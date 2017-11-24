<?php

namespace Phpro\SoapClient\Wsdl\Provider;

/**
 * Class MixedWsdlProvider
 *
 * @package Phpro\SoapClient\Wsdl\Provider
 */
class MixedWsdlProvider implements WsdlProviderInterface
{
    /**
     * This provider passes the user input directly as output.
     * It will let the PHP Soap-client handle errors.
     *
     * {@inheritdoc}
     */
    public function provide(string $source): string
    {
        return $source;
    }
}
