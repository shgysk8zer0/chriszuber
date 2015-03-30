<?php
error_reporting(0);
$PDO = \shgysk8zer0\Core\PDO::load('connect.json');
if (! $PDO->connected) {
	http_response_code(500);
	exit;
}
$all_tags = array_reduce(
	$PDO->query('SELECT `keywords` FROM `posts`')->execute()->getResults(),
	function($all, $tags)
	{
		foreach (explode(',', $tags->keywords) as $tag) {
			$tag = trim($tag);
			if (! in_array($tag, $all)) {
				array_push($all, $tag);
			}
			return $all;
		}
	},
	[]
);
header('Content-Type: application/json');
exit(json_encode($all_tags));
