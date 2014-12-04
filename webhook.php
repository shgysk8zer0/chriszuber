<?php
	error_reporting();
	set_include_path(__DIR__ . DIRECTORY_SEPARATOR . 'classes' . PATH_SEPARATOR . get_include_path());
	spl_autoload_extensions('.class.php');
	spl_autoload_register();

	$webhook = new \core\GitHubWebhook('config/github.json');
	if($webhook->validate()) {
		file_put_contents('hook.json', json_encode($webhook->parsed, JSON_PRETTY_PRINT), FILE_APPEND | LOCK_EX);
	}
	else {
		http_response_code(404);
		exit();
	}
?>
