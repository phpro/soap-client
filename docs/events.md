# Hooking in with events

The `Client` class has a build-in EventDispatcher.
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

- [Logger plugin](plugins/logger.md)
- [Validator plugin](plugins/validator.md)
- [Caching plugin](plugins/caching.md)
