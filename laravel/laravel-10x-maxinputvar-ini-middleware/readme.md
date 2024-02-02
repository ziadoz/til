# Laravel 10.x - Max Input Var INI Middleware

If request contains more inputs than `max_input_vars` INI value, inputs will be truncated.
This can cause the CSRF token to be lost if it's at the end of the form.
This middleware will throw an HTTP 413 exception if truncation occurs.

# Links

https://stackoverflow.com/questions/10303714/php-max-input-vars
https://github.com/symfony/symfony/issues/20262