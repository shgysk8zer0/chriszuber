<?php
error_reporting(0);
$query = array_key_exists('query', $_GET) ? $_GET['query'] : '';
file_put_contents('search.log', print_r([
	'query' => $query,
	'headers' => getallheaders(),
	'raw' => file_get_contents('php://input'),
	'time' => date(DATE_W3C)
], true));
$PDO = \shgysk8zer0\Core\PDO::load('connect.json');
if (! $PDO->connected) {
	http_response_code(500);
	exit;
}
header('Content-Type: application/json');
exit(json_encode([$query, array_reduce(
	$PDO('SELECT `keywords` FROM `posts` LIMIT 25'),
	'reduce_keywords',
	[]
)]));

/**
 * Reduce an array of results from PDO query into a unique array
 *
 * @param  array    $all  Carried array of unique results
 * @param  stdClass $tags Single row item
 * @return array          Array of unique results
 */
function reduce_keywords(array $all, \stdClass $tags)
{
	return array_merge(
		array_filter(
			explode(',', $tags->keywords),
			function($tag) use ($all)
			{
				return ! in_array($tag, $all);
			}
		),
		$all
	);
}
