<?php
namespace shgysk8zer0\Core;
$open_search = new OpenSearch(
	'Super User Blog',
	'Super User Blog Search',
	'favicon.png'
);
$open_search->suggestions_URL = new URL('/searchsuggestions.php?query={searchTerms}');
$open_search->template = new URL('/tags/{searchTems}');
header('Content-Type: ' . $open_search::CONTENT_TYPE);
exit($open_search);
