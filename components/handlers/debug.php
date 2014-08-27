<?php
	$resp = json_response::load();
	check_nonce();
	require_login('admin');

	switch(trim($_POST['debug'])) {

		case 'headers': {
			$resp->info([
				'HTTP Headers' => headers_list()
			]);
		} break;

		case 'extensions': {
			$resp->info([
				'PHP extensions' => get_loaded_extensions()
			]);
		} break;

		case 'modules': {
			$resp->info([
				'Apache Modules' => apache_get_modules()
			]);
		} break;

		case '_SERVER': {
			$resp->info([
				'$_SERVER' => $_SERVER
			]);
		} break;

		case '_SESSION': {
			$resp->info([
				'$_SESSION' => $_SESSION
			]);
		} break;

		case '_COOKIES': {
			$resp->info([
				'$_COOKIES' => $_COOKIE
			]);
		} break;
	}
	$resp->notify(
		'Debug info sent to console.info',
		'Check your developer console'
	);
?>
