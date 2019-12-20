<?php

namespace Phpro\SoapClient;

/**
 * Class Events
 *
 * @package Phpro\SoapClient
 * @deprecated This class will be removed in v2.0. Listen to the FQCN of the events instead!
 */
final class Events
{
    const REQUEST    = 'phpro.soap_client.request';
    const RESPONSE   = 'phpro.soap_client.response';
    const FAULT      = 'phpro.soap_client.fault';
}
