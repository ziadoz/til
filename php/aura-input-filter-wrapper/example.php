<?php
$form = new Form();
$form->fill($_POST);

if ($form->filter()) {
    echo 'valid';
}

$helper = $form->getHelper();

echo $helper->label('Name');
echo $helper->input($form->get('name'));