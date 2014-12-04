<?php
	set_include_path(__DIR__ . DIRECTORY_SEPARATOR . 'classes' . PATH_SEPARATOR . get_include_path());
	spl_autoload_extensions('.class.php');
	spl_autoload_register();

	$webhook = new \core\GitHubWebhook('config/github.json');
	header('content-type: text/plain');
	print_r($webhook);
?>
