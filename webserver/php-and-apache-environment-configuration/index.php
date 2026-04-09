<?php
define('ENV', getenv('ENV') !== false ? getenv('ENV') : 'development');
/* OR */
define('ENV', isset($_SERVER['ENV']) ? $_SERVER['ENV'] : 'development');
