/**
 * Command:  casperjs chomecast-bgs.js [DIR] [START] [END]
 * Examples: casperjs chomecast-bgs.js ~/Downloads/Chromecast
 *           casperjs chomecast-bgs.js ~/Downloads/Chromecast 25
 *           casperjs chomecast-bgs.js ~/Downloads/Chromecast 25 35
 * 
 * Note: [DIR]   (Required) The directory to download. This must exist, it won't be created.
 *       [START] (Optional) Start is the image number to start from, incase the script fails and needs to be restarted.
 *       [END]   (Optional) End is the image number to finish at, if you only want a specific range of images.
 */
var utils  = require('utils'),
	casper = require('casper').create({
		verbose: true,
		pageSettings: {
			loadImages: false,
			loadPlugins: false,
			webSecurityEnabled: false
		}
	});

var path  = casper.cli.get(0);
	start = casper.cli.get(1) - 1 || 0,
	end   = casper.cli.get(2) - 1;

if (! casper.cli.has(0)) {
	casper.echo('Usage: casperjs chromecast-bgs.js [DIR] [START] [END]');
	casper.exit(1);
}

if (path.substr(-1) != '/') {
	path = path + '/';
}

if (start < 0) {
	start = 0;
}

casper.start('https://github.com/dconnolly/Chromecast-Backgrounds');

casper.then(function() {
	this.echo('Downloading Chromecast Wallpapers');

	var links = this.getElementsAttribute('.markdown-body > p > a > img', 'data-canonical-src');

	if (end > (links.length - 1)) {
		end = (links.length - 1);
	}

	links = links.slice(start, end);

	var image = 1;
	this.each(links, function(self, link) {
		this.echo('Downloading Image ' + image + ' of ' + links.length);
		self.download(link, path + 'chromecast-' + image + '.' + utils.fileExt(link));
		image++;
	});
});

casper.run(function() {
	this.echo('Done');
	this.exit();
});