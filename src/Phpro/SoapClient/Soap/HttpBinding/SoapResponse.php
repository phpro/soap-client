<?php

/*
 * This file is part of the Phpro application.
 *
 * Copyright (c) 2015-2017 Phpro
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phpro\SoapClient\Soap\HttpBinding;

/**
 * Class SoapResponse
 *
 * @package Phpro\SoapClient\Soap\HttpBinding
 */
class SoapResponse
{

    /**
     * @var string
     */
    private $response;

    /**
     * SoapResponse constructor.
     *
     * @param string $response
     */
    public function __construct(string $response)
    {
        $this->response = $response;
    }

    /**
     * Get the full SOAP enveloppe response
     *
     * @return string
     */
    public function getResponse(): string
    {
        return $this->response;
    }
}
