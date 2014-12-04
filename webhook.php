<?php
	error_reporting(0);
	$headers = getallheaders();
	if(
		array_key_exists('content-type', $headers)
		and $headers['content-type'] === 'application/json'
		and array_key_exists('User-Agent', $headers)
		and preg_match('/^GitHub-Hookshot/', $headers['User-Agent'])
		and array_key_exists('X-Hub-Signature', $headers)
	) {
		list($algo, $hash) = explode('=', $headers['X-Hub-Signature'], 2);
		$json = file_get_contents('php://input');
		$configFile = __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'github.json';
		$config = json_decode(file_get_contents($configFile));
		$logFile = __DIR__ . DIRECTORY_SEPARATOR . $config->logFile;
		$payloadHash = hash_hmac($algo, $json, $config->secret);
		if($hash !== $payloadHash) {
			http_response_code(404);
			exit();
		}
		else {
			$data = json_decode($json);

			file_put_contents(
				$logFile,
				json_encode($data, JSON_PRETTY_PRINT) . PHP_EOL,
				FILE_APPEND | LOCK_EX
			);
		}
	}
	else {
		http_response_code(404);
		exit();
	}
?>
