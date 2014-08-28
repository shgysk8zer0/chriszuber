<?php
	$resp = json_response::load();
	switch($_POST['load_form']) {
		case 'login': {
			$resp->remove(
				'main > :not(aside)'
			)->prepend(
				'main',
				load_results('forms/login')
			);
			} break;

		case 'new_post': {
			require_login();
			$resp->remove(
				'main > :not(aside)'
			)->prepend(
				'main',
				load_results('forms/new_post')
			);
		} break;

		case 'php_errors': {
			require_login('admin');

			$resp->html(
				'main',
				load_results("forms/php_errors")
			);
		} break;
	}
?>
