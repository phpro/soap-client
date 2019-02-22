# You are in full control of the SOAP client!

To make sure you are in full control of how the SOAP client works, we introduced the SOAP engine.
An Engine contains of a driver and a handler.


## Driver

A driver is responsible for parsing the wsdl, encoding the SOAP request and decoding the SOAP response.


Here is a list of built-in drivers:

- [ExtSoapDriver](drivers/ext-soap.md)
- [Create your own driver](drivers/new.md)


## Handler

A handler is responsible for the HTTP layer.

It enables you to make changes to the request and the response before sending it to the server.
This makes it possible to implement extensions like WSA and WSSE. 

Here is a list of built-in handlers:

- [HttPlugHandle](handlers/httplug.md) (Supports [middlewares](middlewares.md))
- [ExtSoapClientHandle](handlers/ext-soap/client.md)
- [ExtSoapServerHandle](handlers/ext-soap/local-server.md)
- [Create your own handler](handlers/new.md)
