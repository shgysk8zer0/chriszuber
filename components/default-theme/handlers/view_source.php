<?php
$resp = \shgysk8zer0\Core\JSON_Response::load();
try {
	$file = realpath(getenv('AUTOLOAD_DIR')) . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, strtolower($_REQUEST['view_source']));

	if (empty(pathinfo($file, PATHINFO_EXTENSION))) {
		$file .= '.php';
	}
	if (! file_exists($file)) {
		throw new \Exception("{$_REQUEST['view_source']} not found", 200);
	}
	$dialog = new \shgysk8zer0\Core\Elements\Dialog(
		'source_viewer',
		\shgysk8zer0\Core\File::load($file)->highlightFile()
	);

	$resp->append('article', $dialog)->showModal($dialog->id);
} catch(\Exception $e) {
	http_response_code($e->getCode());
	$resp->notify(
		'Exception thrown:',
		$e->getMessage()
	)->error([
		'message' => $e->getMessage(),
		'code' => $e->getCode(),
		'file' => $e->getLine(),
		'trace' => $e->getTrace()
	]);
}
exit($resp);
