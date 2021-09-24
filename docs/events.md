# Hooking in with events

By default, the [generated client factory](cli/generate-clientfactory.md) provides an `EventDispatchingCaller` to the generated client.
This caller makes it possible to listen for SOAP events: 

- `\Phpro\SoapClient\Event\RequestEvent`
- `\Phpro\SoapClient\Event\ResponseEvent`
- `\Phpro\SoapClient\Event\FaultEvent`

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

