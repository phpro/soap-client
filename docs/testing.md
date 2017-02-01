# Testing

As mentioned earlier, it is very easy to integrate this project with [php-vcr](http://php-vcr.github.io/).
 This makes it possible to created fixtures of all your SOAP calls.
 By loading the fixtures, no actual calls will be done to the SOAP endpoint.
 This will make your tests fast, deterministic and accurate!
 Her is an example of a PHPUnit test:
 
```php
/**
 * @test
 * @vcr my-fixture-file.yml
 *
 */
function it_should_greet()
{
    $response = $this->client->helloWorld(new HelloWorldRequest('name'));
    $this->assertEquals('Hello name', $response->getGreeting());
}
```

The first time you run this test, a fixtures file `my-fixture-file.yml` will be created.
 The second time, this file will be used instead of running actual requests.
 Test it out, you will love it!
 
## Configuration

```php
\VCR\VCR::configure()
    ->setCassettePath('test/fixtures/vcr')
    ->enableLibraryHooks(['soap', 'curl'])
;
\VCR\VCR::turnOn();
```

The configuration of php-vcr looks like this. Make sure to only select the library hook you wish to use.
If you are using another handler then the default `SoapHandle`, you might only want to enable curl.
The php-vcr package will overwrite the `SoapClient` which may result in custom methods that cannot be found.
