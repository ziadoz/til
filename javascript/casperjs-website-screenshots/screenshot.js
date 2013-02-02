// Usage: casperjs screenshot.js http://www.bbc.co.uk bbc.png
// https://gist.github.com/2310901

var casper = require('casper').create({
    viewportSize: { width: 1024, height: 768 }
});

var utils = require('utils');

if (casper.cli.args.length < 2) {
    console.error('Usage: screenshot.js URL FILE [OPTIONS]');
    casper.exit();
}

var url = casper.cli.get(0),
    img = casper.cli.get(1);

if (url.indexOf('http://') === -1 && url.indexOf('https://') === -1) {
    url = 'http://' + url;
}

casper.on('load.failed', function() {
    console.log('Could not load webpage.');
    this.exit();
});

casper.on('error', function(msg, backtrace) {
    console.log('Error: ' + msg);
    this.exit();
});

casper.on('timeout', function() {
    console.log('The webpage timed out.');
    this.exit();
});

casper.on('page.error', function(msg, backtrace) {
    console.log('There was an error loading the webpage.');
    this.exit();
});

casper.on('capture.saved', function(file) {
    console.log('Screenshot saved as ' + file + '.');
});

casper.start(url, function() {
    this.capture(img);
});

casper.run();