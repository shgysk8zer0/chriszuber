<?php
	$json = file_get_contents('php://input');
	$data = json_decode($json);
	$file = __DIR__ . DIRECTORY_SEPARATOR . 'hooks.json';
	file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT) . PHP_EOL, FILE_APPEND | LOCK_EX);
	exit();
?>
