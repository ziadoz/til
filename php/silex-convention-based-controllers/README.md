# Silex convention-based controllers

This example shows how you can easily load controllers by convention with silex. The following routes are all valid:

* /
* /index
* /index/index
* /foo
* /foo/index
* /foo/hello
* /foo/hello?name=igorw

A possible extension to this would be passing the application to the controller as well, either to the constructor of the class, or to the method directly. Or using some kind of mapping (possibly with annotations) to inject specific services only.