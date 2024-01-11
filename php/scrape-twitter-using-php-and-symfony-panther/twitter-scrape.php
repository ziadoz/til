<?php
require 'vendor/autoload.php';

use Symfony\Component\Panther\Client;

ini_set('zend.exception_string_param_max_len', 100_000);

foreach (glob('screenshots/*.png') as $screenshot) {
    unlink($screenshot);
}

function env($env) {
    return ($val = getenv($env)) !== false ? $val : throw new Exception('Missing ' . $env . ' environment variable');
}

$credentials = [
    'username' => env('TWITTER_USERNAME'),
    'password' => env('TWITTER_PASSWORD'),
];

$client = Client::createChromeClient(
    arguments: ['--user-agent=Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'],
    baseUri: 'https://twitter.com',
);

// Login
$client->request('GET', '/i/flow/login');
$crawler = $client->getCrawler();

$client->waitForVisibility('[autocomplete=username]');
$crawler->filter('[autocomplete=username]')->sendKeys($credentials['username']);
$crawler->filterXPath('//span[contains(text(), "Next")]/parent::span/parent::div/parent::div')->click();

$client->waitForVisibility('[autocomplete=current-password]');
$crawler->filter('[autocomplete=current-password]')->sendKeys($credentials['password']);
$crawler->filter('[data-testid="LoginForm_Login_Button"]')->click();
