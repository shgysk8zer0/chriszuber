<?php
	$resp = \shgysk8zer0\Core\JSON_Response::load();
	switch($_POST['load_menu']) {
		default:
			$resp->prepend(
				'body',
				load_results("menus/{$_POST['load_menu']}")
			);
	}
?>
