<?php
error_reporting(0);
$PDO = \shgysk8zer0\Core\PDO::load('connect.json');
if (! $PDO->connected) {
	http_response_code(500);
	exit;
}
header('Content-Type: application/json');
exit(json_encode(array_reduce(
	$PDO('SELECT `keywords` FROM `posts`'),
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
)));
