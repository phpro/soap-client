# Log Subscriber

Require any PSR-3 Log implementation:

```bash
composer require psr/log-implementation
```

Register the log subscriber:

```php
use Phpro\SoapClient\Event\Subscriber\LogSubscriber;

$eventDispatcher->addSubscriber(new LogSubscriber($logger));
```

The logger accepts a PSR-3 `LoggerInterface`.
 It will hook in to the Request, Response and Fault event and will log every step of the SOAP process.
 No more code pollution for logging!
