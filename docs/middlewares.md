# Get control of the HTTP layer with middlewares

If the SOAP server has some extensions enabled, it is hard to get them working with the ext-soap client.
In many cases, you'll have to transform the XML request before sending it to the server 
or normalize the response XML before converting it back to objects.
It is also possible to specify some custom HTTP headers to make sure you can authenticate on the remote server.

To make sure your soap-client can work with middlewares, 
you'll have to check if your [data transfer handler](engine.md#handler) supports middlewares.
You can take a look to the list of handlers and check if your handler has the feature "MiddlewareSupporting".

Next, you can use one of the built-in middlewares:

- [BasicAuthMiddleware](#basicauthmiddleware)
- [NtlmMiddleware](#ntlmmiddleware) (deprecated)
- [WsaMiddleware / WsaMiddleware2005](#wsamiddleware)
- [WsseMiddleware](#wssemiddleware)
- [RemoveEmptyNodesMiddleware](#removeemptynodesmiddleware)
- [WS-I Compliance/QuotedSoapActionMiddleware](#ws-i-compliancequotedsoapactionmiddleware)
- [Wsdl/DisableExtensionsMiddleware](#wsdldisableextensionsmiddleware)
- [Wsdl/DisablePoliciesMiddleware](#wsdldisablepoliciesmiddleware)

Can't find the middleware you were looking for?
[It is always possible to create your own one!](#creating-your-own-middleware)


## Built-in middlewares

### BasicAuthMiddleware

In many cases, your server had some kind of authentication enabled.
This basic authentication middleware provides the features you need to add a username and password to the request.

**Usage**
```php
$handler->addMiddleware(new BasicAuthMiddleware('username', 'password'));
```


### NtlmMiddleware

**Deprecated:** since we moved from Guzzle, you will need to write your own NTLM auth implementation. Your best try
is using a cURL Client with the following options: `[CURLOPT_HTTPAUTH => CURLAUTH_NTLM, CURLOPT_USERPWD => 'user:pass']`.

However, take care as there's a [cURL misbehavior](https://github.com/curl/curl/pull/1242) that might block
you going forward with some SOAP servers.

### WsaMiddleware

If your remote server expects Web Service Addressing (WSA) headers to be available in your request,
you can easily activate this middleware.
Internally it used the [wse-php package of robrichards](https://github.com/robrichards/wse-php)
which is a well known library that is used by many developers.
The middleware is a light wrapper that makes it easy to use in your application.

In case you need [WSA w3c 2005 based Web Service Addressing](https://www.w3.org/TR/2005/CR-ws-addr-soap-20050817/#soaphttp), you should use WsaMiddleware2005.

**Dependencies**
```sh
composer require robrichards/wse-php:^2.0
```

**Usage**
```php
$handler->addMiddleware(new WsaMiddleware()); // WSA xmlsoap 2004 based Web Service Addressing

$handler->addMiddleware(new WsaMiddleware2005()); // for WSA w3c 2005 based Web Service Addressing
```


### WsseMiddleware

If you ever had to implement Web Service Security (WSS / WSSE) manually, you know that it is a lot of work to get this one working.
Luckily for you we created an easy to use WSSE middleware that can be used to sign your SOAP requests.

Internally it used the [wse-php package of robrichards](https://github.com/robrichards/wse-php)
which is a well known library that is used by many developers.
The middleware is a light wrapper that makes it easy to use in your application.

**Dependencies**
```sh
composer require robrichards/wse-php:^2.0
```

**Usage**
```php
// Simple:
$wsse = new WsseMiddleware('privatekey.pem', 'publickey.pyb');

// With signed headers. E.g: in combination with WSA:
$wsse = new WsseMiddleware('privatekey.pem', 'publickey.pyb');
$wsse->withAllHeadersSigned();

// With configurable timestamp expiration:
$wsse = new WsseMiddleware('privatekey.pem', 'publickey.pyb');
$wsse->withTimestamp(3600);

// With plain user token:
$wsse = new WsseMiddleware('privatekey.pem', 'publickey.pyb');
$wsse->withUserToken('username', 'password', false);

// With digest user token:
$wsse = new WsseMiddleware('privatekey.pem', 'publickey.pyb');
$wsse->withUserToken('username', 'password', true);

// With end-to-end encryption enabled:
$wsse = new WsseMiddleware('privatekey.pem', 'publickey.pyb');
$wsse->withEncryption('client-x509.pem');
$wsse->withServerCertificateHasSubjectKeyIdentifier(true);

// Add it to the handler
$handler->addMiddleware($wsse);
```


### RemoveEmptyNodesMiddleware

Unset properties are converted into empty nodes in the request xml.
If you need to remove all empty nodes from the request xml, you can simply add the remove empty nodes middleware.

**Usage**
```php
$handler->addMiddleware(new RemoveEmptyNodesMiddleware());
```


### WS-I Compliance/QuotedSoapActionMiddleware

The default SOAP client does not guarantee compatibility with the [WS-I basic profile](http://www.ws-i.org/Profiles/BasicProfile-1.0-2004-04-16.html).
This middleware ensures that the action inside the SOAPAction header is wrapped with double quotes [as specified in rule R2744](http://www.ws-i.org/Profiles/BasicProfile-1.0-2004-04-16.html#R2744). 

**Usage**
```php
use \Phpro\SoapClient\Middleware\WSICompliance\QuotedSoapActionMiddleware;

$wsdlProvier = HttPlugWsdlProvider::create($client);
$wsdlProvider->addMiddleware(new QuotedSoapActionMiddleware());
```


### Wsdl/DisableExtensionsMiddleware

The default SOAP client does not support `wsdl:required` attributes since there is no SOAP extension mechanism in PHP.
You will retrieve this exception: "[SoapFault] SOAP-ERROR: Parsing WSDL: Unknown required WSDL extension" 
when the WSDL does contain required SOAP extensions.
 
This middleware can be used to set the "wsdl:required" 
property to false on the fly so that you don't have to change the WSDL on the server.

**Usage**
```php
$wsdlProvier = HttPlugWsdlProvider::create($client);
$wsdlProvider->addMiddleware(new DisableExtensionsMiddleware());
```


### Wsdl/DisablePoliciesMiddleware

The default SOAP client does not support the [Web Services Policy Framework](http://schemas.xmlsoap.org/ws/2004/09/policy/) attributes since there is no such support in PHP.
You will retrieve this exception: "[SoapFault] SOAP-ERROR: Parsing WSDL: Unknown required WSDL extension 'http://schemas.xmlsoap.org/ws/2004/09/policy'" 
when the WSDL does contains WS policies.
 
This middleware can be used to remove all UsingPolicy and Policy tags on the fly so that you don't have to change the WSDL on the server.

**Usage**
```php
$wsdlProvier = HttPlugWsdlProvider::create($client);
$wsdlProvider->addMiddleware(new DisablePoliciesMiddleware());
```


## Creating your own middleware

Didn't find the middleware you needed? No worries! It is very easy to create your own middleware.
We currently added a thin layer above [HTTPlug plugins](http://docs.php-http.org/en/latest/plugins/index.html) 
to make it easy to modify PSR-7 request and responses.


The easiest way to get you starting is by extending the base `Middleware` class.
You can specify your own `beforeRequest()` and `afterResponse()` method and manipulate whatever you like.

```php
class MyMiddleware extends \Phpro\SoapClient\Middleware\MiddleWare
{
    public function getName(): string
    {
        return 'my_unique_middleware_name';
    }
    
    /**
     * @param callable         $handler
     * @param RequestInterface $request
     * @param array            $options
     *
     * @return PromiseInterface
     */
    public function beforeRequest(callable $handler, RequestInterface $request): Promise
    {
        return $handler($request, $options);
    }

    /**
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    public function afterResponse(ResponseInterface $response): ResponseInterface
    {
        return $response;
    }
}
```

## XML manipulations

Once you get a hang of creating your own middlewares, you'll find out that in many cases you need to manipulate the SOAP XML request or response.
Because this XML is heavily namespaced, we created a `SoapXml` manipulator class.

Example usage:

```php
// Load the XML from a PSR7 request or response:
$xml = SoapXml::fromStream($request->getBody());

// Fetch specific elements:
$dom = $xml->getXmlDocument();
$enveloppe = $xml->getEnvelope();
$headers = $xml->getHeaders();
$body = $xml->getBody();
$namespace = $xml->getSoapNamespaceUri();

// XML manipulations:
$xml->addEnvelopeNamespace('alias', 'http://alias');
$newHeader = $xml->createSoapHeader();
$xml->prependSoapHeader($newHeader);

// Xpath functions:
$xml->registerNamespace('alias', 'http://alias');
$xml->xpath('/soap:Envelope')->item(0);

// Use the manipulated XML in your PSR7 request or response:
$request = $request->withBody($xml->toStream())
```

As you can see, this XML manipulation class is rather small at the moment but is super powerful.
Internally it uses `DOMDocument` to make it possible to manipulate every little XML detail you want.
