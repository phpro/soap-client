<?php

namespace Phpro\SoapClient\Wsdl\Loader;

interface WsdlLoaderInterface
{
    /**
     * Load the URL or file and return it's contents
     * The difference between a WsdlProviderInterface is the fact that ext-soap is not able to automatically
     * use the content of a wsdl file. Therefore a little transformation is needed.
     */
    public function load(string $wsdl): string;
}
