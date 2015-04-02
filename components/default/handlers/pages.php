<?php
$page = \shgysk8zer0\Core\Pages::load();
$head = $DB->fetchArray(
	"SELECT `value` FROM `head`
	WHERE `name` = 'title'"
, 0);

$canonical = new \shgysk8zer0\Core\URL("//{$_SERVER['SERVER_NAME']}");
if (in_array('mod_ssl', apache_get_modules())) {
	$canonical->scheme = 'https';
} else {
	$canonical->scheme = 'http';
}

exit(\shgysk8zer0\Core\JSON_Response::load()->remove(
	'main > :not(aside)'
)->prepend(
	'main',
	$page->content
)->scrollTo(
	'main :first-child'
)->attributes(
	'meta[name=description], meta[itemprop=description], meta[property="og:description"]',
	'content',
	$page->description
)->attributes(
	'meta[name=keywords], meta[itemprop=keywords]',
	'content',
	$page->keywords
)->attributes(
	'link[rel=canonical]',
	'href',
	"$canonical"
)->attributes(
	'meta[itemprop=url], meta[property="og:url"]',
	'content',
	"$canonical"
)->attributes(
	'meta[itemprop=name], meta[property="og:title"]',
	'content',
	"{$page->title} | {$head->value}"
)->text(
	'head > title',
	"{$page->title} | {$head->value}"
));
