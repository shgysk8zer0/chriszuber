<?php
	$resp = \shgysk8zer0\Core\JSON_Response::load();
	switch($_REQUEST['template']) {
		default: {
			$template = get_template($_REQUEST['template']);
		}
	}

	if($template) $resp->template = preg_replace('/\%.+\%/', null, $template);
?>
