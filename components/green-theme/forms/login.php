<?php
$dialog = $doc->appendChild($doc->createElement('dialog'));
$dialog->{'@id'} = 'loginDialog';
$form = $dialog->appendChild($doc->createElement('form'));
$form->{'@name'} = 'login';
$form->{'@action'} = URL;
$form->{'@method'} = 'POST';
$fieldset = $form->appendChild($doc->createElement('fieldset'));
$fieldset->{'@form'} = 'login';
$fieldset->legend('Login')
	->label(
		['@for' => 'user', '@data-icon' => '@']
	)->input([
		'@type' => 'email',
		'@name' => 'user',
		'@id' => 'user',
		'@placeholder' => 'user@example.com',
		'@required' => null
	])->br(null)->label([
		'@for' => 'password',
		'@data-icon' => 'x'
	])->input([
		'@type' => 'password',
		'@name' => 'password',
		'@id' => 'password',
		'@pattern' => pattern('password'),
		'@placeholder' => 'What\'s the password',
		'@required' => null
	])->button([
		'@type' => 'submit',
		'@title' => 'Login',
		'@data-icon' => 'X'
	])->button([
		'@type' => 'reset',
		'@title' => 'Reset Form',
		'@data-icon' => 'V'
	])->button([
		'@type' => 'button',
		'@title' => 'Close Dialog',
		'@data-close' => '#loginDialog'
	]);
echo $dialog;
