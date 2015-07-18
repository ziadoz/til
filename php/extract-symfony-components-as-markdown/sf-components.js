// http://symfony.com/components

var components = [];

$('.components-list tr').each(function() {
    var name = $.trim($(this).find('td.name').text()),
        link = 'http://' + document.domain + $(this).find('td.name a').attr('href'),
        desc = $.trim($(this).find('td:eq(1)').text());

    if (name && link && desc) {
        components.push('[' + name +'](' + link + ') - ' + desc);
    }
});

components.join("\n");

/*
[Asset](http://symfony.com/components/Asset) - Manages URL generation and versioning of web assets such as CSS stylesheets, JavaScript files and image files.
[BrowserKit](http://symfony.com/components/BrowserKit) - Simulates the behavior of a web browser.
[ClassLoader](http://symfony.com/components/ClassLoader) - Loads your project classes automatically if they follow some standard PHP conventions.
[Config](http://symfony.com/components/Config) - Helps you find, load, combine, autofill and validate configuration values.
[Console](http://symfony.com/components/Console) - Eases the creation of beautiful and testable command line interfaces.
[CssSelector](http://symfony.com/components/CssSelector) - Converts CSS selectors to XPath expressions.
[Debug](http://symfony.com/components/Debug) - Provides tools to ease debugging PHP code.
[DependencyInjection](http://symfony.com/components/DependencyInjection) - Allows you to standardize and centralize the way objects are constructed in your application.
[DomCrawler](http://symfony.com/components/DomCrawler) - Eases DOM navigation for HTML and XML documents.
[EventDispatcher](http://symfony.com/components/EventDispatcher) - Implements the Mediator pattern in a simple and effective way to make projects truly extensible.
[ExpressionLanguage](http://symfony.com/components/ExpressionLanguage) - Provides an engine that can compile and evaluate expressions.
[Filesystem](http://symfony.com/components/Filesystem) - Provides basic utilities for the filesystem.
[Finder](http://symfony.com/components/Finder) - Finds files and directories via an intuitive fluent interface.
[Form](http://symfony.com/components/Form) - Provides tools to easy creating, processing and reusing HTML forms.
[HttpFoundation](http://symfony.com/components/HttpFoundation) - Defines an object-oriented layer for the HTTP specification.
[HttpKernel](http://symfony.com/components/HttpKernel) - Provides the building blocks to create flexible and fast HTTP-based frameworks.
[Icu](http://symfony.com/components/Icu) - Contains the data of the ICU library in a specific version. This component is deprecated since October 2014, use the Intl component instead.
[Intl](http://symfony.com/components/Intl) - Provides fallback code to handle cases when the intl extension is missing.
[Locale](http://symfony.com/components/Locale) - Provides fallback code to handle cases when the intl extension is missing. This component is deprecated since 2.3, use the Intl component instead.
[OptionsResolver](http://symfony.com/components/OptionsResolver) - Helps you configuring objects with option arrays.
[Process](http://symfony.com/components/Process) - Executes commands in sub-processes
[PropertyAccess](http://symfony.com/components/PropertyAccess) - Provides function to read and write from/to an object or array using a simple string notation.
[Routing](http://symfony.com/components/Routing) - Maps an HTTP request to a set of configuration variables.
[Security](http://symfony.com/components/Security) - Provides an infrastructure for sophisticated authorization systems.
[Serializer](http://symfony.com/components/Serializer) - Turns objects into a specific format (XML, JSON, Yaml, ...) and the other way around.
[Stopwatch](http://symfony.com/components/Stopwatch) - Provides a way to profile code.
[Templating](http://symfony.com/components/Templating) - Provides all the tools needed to build any kind of template system.
[Translation](http://symfony.com/components/Translation) - Provides tools to internationalize your application.
[Validator](http://symfony.com/components/Validator) - Provides tools to validate classes.
[VarDumper](http://symfony.com/components/VarDumper) - vardumper.summary
[Yaml](http://symfony.com/components/Yaml) - Loads and dumps YAML files.
 */