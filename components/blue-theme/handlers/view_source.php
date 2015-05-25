<?php
$resp = \shgysk8zer0\Core\JSON_Response::load();
try {
	$file = strtolower(join(
		DIRECTORY_SEPARATOR,
		[
			BASE,
			'classes',
			str_replace('\\', DIRECTORY_SEPARATOR, trim($_REQUEST['view_source'], '\\'))
		]
	));
	if (! file_exists($file)) {
		throw new \Exception("{$_REQUEST['view_source']} not found", 200);
	}
	$dialog = new \shgysk8zer0\Core\HTML_El('dialog', null, null, true);
	$dialog->{'@id'} = 'source_viewer_dialog';
	$dialog->button = ['@data-delete' => '#source_viewer_dialog'];
	$dialog->br = null;
	$dialog(\shgysk8zer0\Core\File::load($file)->highlightFile());

	$resp->append(
		'article',
		$dialog
	)->showModal('#source_viewer_dialog');
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
