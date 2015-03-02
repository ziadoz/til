<?php
// See Error Handling in PHP: https://nomadphp.com/2015/02/25/nomadphp-2015-02-us-lt2/#

$display = function ($status, $headers = array(), $content = '') {
    http_response_code($status);

    foreach ($headers as $key => $value) {
        header($key . ': ' . $value);
    }

    echo $content;
};

$dispatch = function ($display, $response) {
    return call_user_func_array($display, $response);
};

$controller = function ($get, $post, $cookie, $server) {
    $content = '<p>Hello, ' . (isset($get['name']) ? $get['name'] : 'World') . '!</p>';
    return array(200, array('X-Powered-By' => 'Awesome'), $content);
};

$dispatch($display, $controller($_GET, $_POST, $_COOKIE, $_SERVER));