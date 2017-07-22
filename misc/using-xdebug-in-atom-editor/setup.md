## Using XDebug in Atom Editor
Install the [XDebug plugin for Atom](https://github.com/gwomacks/php-debug) and then add the following to the `config.cson` file (Atom > Config…):

```
"php-debug":
  PathMaps: [
    "remotepath;localpath"
    "/server/path/to/project/;/local/path/to/project/"
  ]
  ServerPort: 9001
```

_Note: The trailing slashes and full paths are important, or breakpoints will be ignored. The default port is 9000. Use 9001 to avoid clashes with PHP-FPM._

Install the [Chrome XDebug Helper extension](https://chrome.google.com/webstore/detail/xdebug-helper/eadndfjplgieldjbigjakmdgkmoaaaoc?hl=en) and set the IDE key in the options to the value in your `/xdebug.ini`. You can then click on the extention to enable or disable XDebug.
