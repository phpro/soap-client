# Logger plugin

The logger plugin is activated automatically when you attach a `LoggerInterface` to the `ClientBuilder`.
 It will hook in to the Request, Response and Fault event and will log every step of the SOAP process.
 No more code pollution for logging!