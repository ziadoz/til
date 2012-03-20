#!/usr/bin/env php
<?php
function main($argv) {       
	$opts = getopt(null, array('min:', 'max:'));
	
	$min = (int) isset($opts['min']) ? trim($opts['min']) : 0;
	$max = (int) isset($opts['max']) ? trim($opts['max']) : 100;
	
	puts("Guess The Number \n", 'green');
	puts("I'm thinking of a number between $min and $max \n\n");

	$tries = 0;
	$number = rand($min, $max);
	while (true) {
		if ($tries > 0) {
			puts('Try again: ');
		} else {
			puts('Take a wild guess: ');
		}
		
		$guess = trim(gets());
		$tries++;
		
		if (! is_numeric($guess)) {
			puts("It helps if you enter a number. \n", 'red');
		} elseif($guess < $min) {
			puts("Well it can't be less than $min can it. \n", 'red');
		} elseif($guess > $max) {
			puts("Well it can't be more than $max can it. \n", 'red');
		} elseif ($guess < $number) {
			puts("Try going higher. \n", 'red');
		} elseif ($guess > $number) {
			puts("Try going lower. \n", 'red');
		} else {
			puts("Correct, it was $number! \n", 'yellow');
			puts("It took you $tries guesses. \n", 'yellow');
			exit(0);
		}
	}
}

function gets() {
	return fgets(STDIN);
}

function puts($text, $style = null) {
	$styles = array(	
		'bold'		=> "\033[1m%s\033[0m",
		'black'		=> "\033[0;30m%s\033[0m",
		'red'		=> "\033[0;31;31m%s\033[0m",
		'green' 	=> "\033[0;32m%s\033[0m",
		'yellow'	=> "\033[0;33m%s\033[0m",
		'blue'		=> "\033[0;34m%s\033[0m",
		'magenta'	=> "\033[0;35m%s\033[0m",
		'cyan'		=> "\033[0;36m%s\033[0m",
	);
	
	fwrite(STDOUT, sprintf(isset($styles[$style]) ? $styles[$style] : "%s", $text));
}

if ('cli' === php_sapi_name() && basename(__FILE__) === basename($argv[0])) {
	main($argv);
}