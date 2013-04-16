<?php
function parse_youtube_embed($embed) {
    if (empty($embed)) {
		return false;
	}

	$dom = new DOMDocument;
	if (! $dom->loadHTML($embed)) {
		return false;
	}

	$iframe = $dom->getElementsByTagName('iframe')->item(0);
	if (! $iframe) {
		return false;
	}

	$results = array(
		'src' 	 => $iframe->getAttribute('src'),
		'width'  => $iframe->getAttribute('width'),
		'height' => $iframe->getAttribute('height'),
	);

	if (empty($results['src'])) {
		return false;
	}

	return $results;
}

$embed   = '<iframe width="1280" height="720" src="http://www.youtube.com/embed/bLHW78X1XeE" frameborder="0" allowfullscreen></iframe>';
$youtube = parse_youtube_embed($embed);

print_r($youtube);