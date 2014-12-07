<?php
	if($_SERVER['HTTP_HOST'] === $_SERVER['SERVER_NAME'] and ($_SERVER['CONTENT_TYPE'] === 'application/json' or $_SERVER['CONTENT_TYPE'] === 'application/csp-report')) {
		require_once('./functions.php');
		$DB =\core\PDO::load('connect.ini');
		$report = json_decode(trim(file_get_contents('php://input')), true)['csp-report'];

		if(!array_key_exists('source-file', $report)) $report['source-file'] = 'unknown';
		if(!array_key_exists('script-sample', $report)) $report['script-sample'] = '';

		$DB->prepare("
			INSERT INTO `CSP_errors` (
				`blocked-uri`,
				`document-uri`,
				`violated-directive`,
				`source-file`,
				`script-sample`
			)
			VALUES (
				:blocked_uri,
				:document_uri,
				:violated_directive,
				:source_file,
				:script_sample
			)
		")->bind([
			'blocked_uri' => $report['blocked-uri'],
			'document_uri' => $report['document-uri'],
			'violated_directive' => $report['violated-directive'],
			'source_file' => $report['source-file'],
			'script_sample' => $report['script-sample']
		])->execute();
	}
	else {
		http_response_code(403);
	}
?>
