<?php
// Mode.
define('MODE', isset($_SERVER['MODE']) ? $_SERVER['MODE'] : 'development');

// Character Set.
ini_set('default_charset', 'UTF-8');
mb_internal_encoding('UTF-8');
mb_regex_encoding('UTF-8');
mb_http_input('UTF-8');
mb_http_output('UTF-8');

// Error Reporting.
ini_set('log_errors', 1);
ini_set('display_errors', 0);
error_reporting(-1);

if (MODE !== 'production') {
    ini_set('display_errors', 1);
}

// I18N.
setlocale(LC_ALL, 'en_CA');
locale_set_default('en_CA');
date_default_timezone_set('UTC');

// Session.
ini_set('session.cookie_httponly', 1);
session_name('APPSESS');
session_start();