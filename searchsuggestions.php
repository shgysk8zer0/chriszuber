<?php
error_reporting(0);
define('PARAM', 'query');
if (! array_key_exists(PARAM, $_REQUEST)) {
	http_response_code(400);
	exit;
}
$PDO = \shgysk8zer0\Core\PDO::load('connect.json');
if (! $PDO->connected) {
	http_response_code(500);
	exit;
}
header('Content-Type: application/json');
$matches = $PDO->prepare(
	'SELECT DISTINCT(`keywords`) as `tags` FROM `posts` WHERE `keywords` LIKE :tags'
);
$matches->tags = '%' . str_replace(' ', '%', $_REQUEST[PARAM]) . '%';
$results = array_reduce(
	$matches->execute()->getResults(),
	'reduce_tags',
	array()
);
exit(json_encode($results));

/**
 * Reduce an array of results from PDO query into a unique array
 *
 * @param  array    $results  Carried array of unique results
 * @param  stdClass $tags     Single row item
 * @return array              Array of unique results
 */
function reduce_tags(array $results = array(), \stdClass $post)
{
	foreach (explode(',', $post->tags) as $tag) {
		$tag = trim(strtolower($tag));
		if (! in_array(['phrase' => $tag], $results) and preg_match('/^' . preg_quote($tag) .'/i', $_REQUEST[PARAM])) {
			array_push($results, ['phrase' => $tag]);
		}
	}
	return $results;
}
