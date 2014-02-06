<?php 
require 'admin/runtime.php'; 

perch_content_create('Example Shared Region', array(
    'template' => 'text_block.html',
    'shared'   => true,
));
?>

<!DOCTYPE>
<html lang="en">
<head>
    <title>Perch Website</title>
</head>
<body>