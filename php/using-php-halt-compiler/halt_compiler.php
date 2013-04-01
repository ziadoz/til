<?php
/**
 * The __halt_compiler() function will stop the PHP compiler when called.
 * You can then use the __COMPILER_HALT_OFFSET__ constant to grab the contents of the PHP file after the halt.
 * In this example a PHP template is stored after the halt, to allow simple separation of logic from templating.
 * The template is stored in a temporary file so it can be included and parsed.
 *
 * See: https://github.com/bobthecow/mustache.php/blob/dev/src/Mustache/Loader/InlineLoader.php
 *  	http://php.net/manual/en/function.halt-compiler.php
 */
function get_halt_data() {
    return file_get_contents(__FILE__, false, null, __COMPILER_HALT_OFFSET__);
}

function create_tmp_template($data) {
    $tmpfile = tempnam(sys_get_temp_dir(), 'halt_compiler_template');
    file_put_contents($tmpfile, $data);
    return $tmpfile;
}

function parse_template($template, $data) {
    $data = (array) $data;
    ob_start();
    extract($data);
    include $template;
    return ob_get_clean();
}

$template = create_tmp_template(get_halt_data());

$output = parse_template($template, array(
    'title'     => 'Hello, World!',
    'paragraph' => 'This is an exciting paragraph of text.',
));

echo $output;
unlink($template);

__halt_compiler();

<!-- Layout Template -->
<!DOCTYPE>
<html>
<head>
    <meta charset="utf-8" />
    <title><?php echo $title; ?></title>
</head>
<body>
    <h1><?php echo $title; ?></h1>
    <p><?php echo $paragraph; ?></p>
</body>
</html>