<?php
	$resp = \shgysk8zer0\core\json_response::load();
	switch($_POST['load_menu']) {
		default:
			$resp->prepend(
				'body',
				load_results("menus/{$_POST['load_menu']}")
			);
	}
?>
