<?php
	if($_SERVER['HTTP_HOST'] === $_SERVER['SERVER_NAME'] and ($_SERVER['CONTENT_TYPE'] === 'application/json' or $_SERVER['CONTENT_TYPE'] === 'application/csp-report')){
		require_once('./functions.php');
		$DB = new _pdo;
		$log = 'csp_errors.log';
		$JSON = file_get_contents('php://input');
		file_put_contents('./csp.log', $JSON . PHP_EOL,  FILE_APPEND |  LOCK_EX );
		$data = json_decode($JSON, true);
		$report = <<<EOT
BLOCKED: "{$data['csp-report']['blocked-uri']}" on "{$data['csp-report']['document-uri']}" due to rule "{$data['csp-report']['violated-directive']}".\n
EOT;
		//file_put_contents($log, $report, FILE_APPEND);
		$DB->array_insert('CSP_errors', array('error' => $DB->escape($report)));
		print_r($data['csp-report']);
	}
	else header("HTTP/1.1 403 Forbidden");
?>
