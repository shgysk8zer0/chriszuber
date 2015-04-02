<?php
error_reporting(0);
header('Content-Type: application/json');
define('BASE', __DIR__);

$report = trim(file_get_contents('php://input'));
$headers = getallheaders();
$headers = (object)array_combine(
	array_map('strtolower', array_keys($headers)),
	array_values($headers)
);

try {
	if($_SERVER['HTTP_HOST'] !== $_SERVER['SERVER_NAME']) {
		throw new \Exception('Unauthorized', 401);
	}
	if(
		preg_match('/^application\/(json|csp-report)/',
		$headers->{'content-type'}
	)) {
		if(
			is_null($headers->{'content-length'})
			or strlen($report) !== (int)$headers->{'content-length'}
		) {
			throw new \Exception('Content-Length not set or invalid', 411);
		}

		$report = json_decode($report)->{'csp-report'};

		if(! is_object($report) or empty($report)) {
			throw new \Exception('Invalid csp-report', 400);
		}
		if(! isset($report->{'script-sample'})) {
			$report->{'script-sample'} = '';
		}
		if(! isset($report->{'source-file'})) {
			$report->{'source-file'} = null;
		}

		$DB = new \shgysk8zer0\Core\PDO('connect.json');

		if (! $DB->connected) {
			throw new \Exception('Database connection failed', 504);
		}

		$stm = $DB->prepare(
			"INSERT INTO `CSP_errors` (
				`blocked-uri`,
				`document-uri`,
				`violated-directive`,
				`source-file`,
				`script-sample`
			) VALUES (
				:blocked_uri,
				:document_uri,
				:violated_directive,
				:source_file,
				:script_sample
			);"
		)->bind([
			'blocked_uri' => $report->{'blocked-uri'},
			'document_uri' => $report->{'document-uri'},
			'violated_directive' => $report->{'violated-directive'},
			'source_file' => $report->{'source-file'},
			'script_sample' => $report->{'script-sample'}
		]);

		if(! $stm->execute()) {
			throw new \Exception('Error not successfully recorded', 504);
		} else {
			http_response_code(202);
			exit(json_encode($report, JSON_PRETTY_PRINT));
		}
	} else {
		throw new \Exception('Invalid Content-Type header', 405);
	}
} catch(\Exception $e) {
	http_response_code($e->getCode());
	exit(json_encode([
		'Message' => $e->getMessage(),
		'Request' => [
			'Headers' => $headers,
			'Body' => $report
		]
	], JSON_PRETTY_PRINT));
}
