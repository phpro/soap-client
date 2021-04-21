# Creating your own HTTP handler

You can create your own HTTP handler by implementing the `HandlerInterface`

```php
<?php

use Phpro\SoapClient\Soap\Handler\HandlerInterface;
use Phpro\SoapClient\Soap\HttpBinding\SoapRequest;
use Phpro\SoapClient\Soap\HttpBinding\SoapResponse;
use Phpro\SoapClient\Soap\HttpBinding\LastRequestInfo;

class YourHandler implements HandlerInterface
{
    // Transform a soap request into a soap response:
    public function request(SoapRequest $request): SoapResponse;
    
    // Collect the last handled request and response information:
    public function collectLastRequestInfo(): LastRequestInfo;
}
```

Alternatively you might extend `Phpro\SoapClient\Middleware` which implements some boilerplate.

## Adding additional features to your custom handler

- Allow [HTTP middlewares](../middlewares.md) by implementing the `Phpro\SoapClient\Middleware\MiddlewareSupportingInterface`.
