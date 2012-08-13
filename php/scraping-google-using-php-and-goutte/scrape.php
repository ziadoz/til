<?php
/**
 * Todo: Send a random user agent string and sleep a random amount between requests.
 */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	// Extract and sanatize input:
	$domain = filter_input(INPUT_POST, 'domain', FILTER_SANITIZE_URL);
	$terms = filter_input(INPUT_POST, 'terms', FILTER_SANITIZE_STRING);
	
	// Setup Goutte (which also includes Guzzle):
	// Goutte: https://github.com/fabpot/Goutte
	// Guzzle: https://github.com/guzzle/guzzle
	require __DIR__ . '/goutte.phar';

	// Build up a search URL:
	$pages = 10;
	$url = 'http://www.google.ca/search?' . http_build_query(array('q' => $terms));

	// Request search results:
	$client = new Goutte\Client;
	$crawler = $client->request('GET', $url);

	// See response content:
	// $response = $client->getResponse();
	// $response->getContent();

	// Start crawling the search results:
	$page = 1;
	$result = null;

	while (is_null($result) || $page <= $pages) {
		// If we are moving to another page then click the paging link:
		if ($page > 1) {
			$link = $crawler->selectLink($page)->link();
			$crawler = $client->click($link);
		}

		// Use a CSS filter to select only the result links:
		$links = $crawler->filter('li.g > h3 > a');

		// Search the links for the domain:
		foreach ($links as $index => $link) {	
			$href = $link->getAttribute('href');
			if (strstr($href, $domain)) {
				$result = ($index + 1) + (($page - 1) * 10);
				break 2;
			}
		}

		$page++;
	}
}

// A simple HTML escape function:
function escape($string = '') {
	return htmlspecialchars($string, ENT_COMPAT, 'UTF-8', false);
}
?>

<!DOCTYPE html>
<head>
	<title>Scrape Google with Goutte</title>
	<meta charset="utf-8" />
</head>
<body>	
	<h1>Scrape Google with Goutte: </h1>
	<form action="." method="post" accept-charset="UTF-8">
		<label>Domain: <input type="text" name="domain" value="<?php echo isset($domain) ? escape($domain) : ''; ?>" /></label>
		<label>Search Terms: <input type="text" name="terms" value="<?php echo isset($terms) ? escape($terms) : ''; ?>" /></label>
		<input type="submit" value="Scrape Google" />
	</form>

	<?php if (isset($domain, $terms, $url, $result, $page)) : ?>
		<h1>Scraping Results:</h1>
		<p>Searching Google for <b><?php echo escape($domain); ?></b> using the terms <i>"<?php echo escape($terms); ?>"</i>.</p>
		<p><a href="<?php echo escape($url); ?>" target="_blank">See Actual Search Results</a></p>
		<p>Result Number: <?php echo escape($result); ?></p>
		<p>Page Number: <?php echo escape($page); ?></p>
	<?php endif; ?>
</body>
</html>