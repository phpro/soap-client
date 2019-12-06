# Hooking in with events

The `Client` class uses an EventDispatcher which can be configured inside the [generated client factory](cli/generate-clientfactory.md).
 It will trigger events at all important phases of the SOAP call: 

- `\Phpro\SoapClient\Event\RequestEvent`: (Name : Events::REQUEST)
- `\Phpro\SoapClient\Event\ResponseEvent`: (Name : Events::RESPONSE)
- `\Phpro\SoapClient\Event\FaultEvent`: (Name : Events::FAULT)

You can subscribe your own listeners to the configured `EventDispatcher`. For example:

```php
class ResponseFailedSubscriber implements SubscriberInterface
{
    // implement interface
}

$dispatcher->addSubscriber(new ResponseFailedSubscriber());
```

This package ships with some default subscriber plugins:

- [Logger subscriber](event-subscribers/logger.md)
- [Validator subscriber](event-subscribers/validator.md)
- [Caching subscriber](event-subscribers/caching.md)

