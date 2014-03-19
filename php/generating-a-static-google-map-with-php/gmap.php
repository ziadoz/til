<?php 
$url = 'http://maps.googleapis.com/maps/api/staticmap?';
$bits = array(
    'center'  => 'Calgary Tower, Calgary, AB, Canada',
    'zoom'    => '16',
    'size'    => '800x600',
    'maptype' => 'roadmap',
    'markers' => 'color:0x576d4e|label:N|49.6967179,-112.8450119',
    'sensor'  => 'false',
);

echo '<img src="' . $url . http_build_query($bits) . '" alt="" />';