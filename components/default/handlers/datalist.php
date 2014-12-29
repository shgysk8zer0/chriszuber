<?php
	$resp = \shgysk8zer0\Core\json_response::load();
	switch($_REQUEST['datalist']) {
		case 'tags': {
			$datalist = get_datalist('tags');
		} break;

		case 'PHP_errors_files': {
			require_login('admin');
			$datalist = get_datalist('PHP_errors_files');
		} break;
	}
	if(isset($datalist)) {
		$resp->after(
			"[list=\"{$_REQUEST['datalist']}\"]",
			$datalist
		);
	}
?>
