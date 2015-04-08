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
	function(array $all, \stdClass $tags)
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
	},
	[]
)));
