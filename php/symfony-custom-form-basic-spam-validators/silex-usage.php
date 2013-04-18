<?php
use Ziadoz\Validator\Constraints as CustomAssert;

$form = $app['form.factory']->createBuilder('form', array());
$form->add('message', 'textarea', array(
    'constraints' => array(
        new CustomAssert\ContainsHtml(),
        new CustomAssert\ContainsLinks(array('max' => 4)),
    ),
));