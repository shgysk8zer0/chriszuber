<?php
$open_search = new \shgysk8zer0\Core\OpenSearch(
	'Super User Blog',
	'Super User Blog Search',
	'favicon.png'
);
$open_search->suggestions_URL = $_SERVER['REQUEST_SCHEME'] . '://'
	. $_SERVER['HTTP_HOST'] . '/searchsuggestions.php';
$open_search->template = 'tags/{searchTerms}';
header('Content-Type: ' . $open_search::CONTENT_TYPE);
exit($open_search);
