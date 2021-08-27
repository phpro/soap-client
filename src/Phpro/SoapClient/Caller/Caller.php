<?php

declare(strict_types=1);

namespace Phpro\SoapClient\Caller;

use Phpro\SoapClient\Type\RequestInterface;
use Phpro\SoapClient\Type\ResultInterface;

interface Caller
{
    public function __invoke(string $method, RequestInterface $request): ResultInterface;
}
