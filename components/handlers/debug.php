<?php
	$resp = json_response::load();
	check_nonce();
	require_login('admin');

	switch(trim($_POST['debug'])) {

		case 'headers': {
			$resp->info([
				'HTTP Headers' => headers_list()
			]);
		}break;

		case 'extensions': {
			$resp->info([
				'PHP extensions' => get_loaded_extensions()
			]);
		}break;

		case 'modules': {
			$resp->info([
				'Apache Modules' => apache_get_modules()
			]);
		}break;

		default: {
			$resp->info([
				$_POST['debug'] => $$_POST['debug']
			]);
		}
	}
	$resp->notify(
		'Debug info sent to console.info',
		'Check your developer console'
	);
?>
