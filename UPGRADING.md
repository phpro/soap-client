# V1 to V2


## Dependency upgrades

* Symfony to LTS (4.4)
* PHP (^8.0)

## Removed deprecations

### Events

The custom event dispatchers are removed.
We now support any [PSR-14 event dispatcher](https://www.php-fig.org/psr/psr-14/).

Fully qualified class names will be used as event names.
The old deprecated event names are now removed from the codebase.

The events won't contain the soap client anymore.
We don't want them to be service containers.
So instead, if you require the soap client in the event listeners, you need to inject them manually.

## Client

We don't work with a base client anymore.
Instead, a `Caller` is injected into the client you fully own.
The caller is responsible for transporting the request.
We provide an engine caller and an event dispatching caller
so that your client keeps on working how you expect it to.

We removed the debugging method from the soap-client.
Instead, you can either debug the request or result directly.
If you want to have access to the HTTP soap payload, we suggest adding a logger plugin to the HTTP client.

This implies:

* Changes to the client generator
* Changes to the client factory generator



