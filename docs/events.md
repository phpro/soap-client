# Hooking in with events

The `Client` class uses an EventDispatcher which can be configured inside the [generated client factory](cli/generate-clientfactory.md).
 It will trigger events at all important phases of the SOAP call: 

- Events::REQUEST (RequestEvent)
- Events::RESPONSE (ResponseEvent)
- Events::FAULT (FaultEvent)

You can subscribe your own listeners to the configured `EventDispatcher`. For example:

```php
class ResponseFailedSubscriber implements SubscriberInterface
{
    // implement interface
}

$dispatcher->addSubscriber(new ResponseFailedSubscriber());
```

This package ships with some default subscriber plugins:

- [Logger subscriber](event-subscriber/logger.md)
- [Validator subscriber](event-subscriber/validator.md)
- [Caching subscriber](event-subscriber/caching.md)

