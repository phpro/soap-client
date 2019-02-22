# Validator Subscriber

Require the symfony validator:

```bash
composer require symfony/validator
```

Register the validator subscriber:

```php
use Phpro\SoapClient\Event\Subscriber\ValidatorSubscriber;

$eventDispatcher->addSubscriber(new ValidatorSubscriber($validator));
```

It is possible to use the [Symfony validator component](https://symfony.com/doc/current/components/validator.html)
to validate your request objects before sending them to the server.
Since some servers return very cryptographic errors, 
the validation of request components could save you a lot of time during development.

It will hook in to the Request event and will throw a `RequestException`
when your request object doesn't contain valid data.

No more crappy error messages from the soap server!
