# Caching Subscriber

This repository does not contain a caching subscriber.
 If you want to be able to cache the methods in your SOAP client, you could use our 
 [annotated-cache](https://github.com/phpro/annotated-cache) package.
 
 This package will make it possible to specify the caching configuration in the annotations of your SOAP client.
 
```php

use Phpro\AnnotatedCache\Annotation\Cacheable;

class YourClient extends Client
{
    /**
     * @Cacheable(pools="soapclient-pool", key="request", tags="helloworld", ttl=500)
     */
    public function helloWorld(RequestInterface $request)
    {
        return $this->call('HelloWorld', $request);
    }
}
```
