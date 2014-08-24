<?php
	$resp = json_response::load();
	check_nonce();
	require_login('admin');

	ob_start();
	switch(trim($_POST['debug'])) {

		case 'headers': {
			debug(headers_list());
		}break;

		case 'extensions': {
			debug(get_loaded_extensions());
		}break;

		case 'modules': {
			debug(apache_get_modules());
		}break;

		default: {
			debug($$_POST['debug']);
		}
	}
	$resp->remove(
		'main > :not(aside)'
	)->prepend(
		'body > main',
		ob_get_clean()
	);
?>