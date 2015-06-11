<?php
try {
	if (! array_key_exists('gist', $_GET)) {
		throw new \Exception('No Gist requested', 404);
	}
	$url = new \shgysk8zer0\Core\URL();
	$url->scheme = 'https://';
	$url->host = 'gist.github.com';
	$url->path = "{$_GET['gist']}.js";
	$dom = new \DOMDocument('1.0', 'UTF-8');
	$dom->loadHTML('<!DOCTYPE html>');
	$html = $dom->appendChild($dom->createElement('html'));
	$head = $html->appendChild($dom->createElement('head'));
	$body = $html->appendChild($dom->createElement('body'));
	$charset = $head->appendChild($dom->createElement('meta'));
	$charset->setAttribute('charset', 'utf-8');
	$head->appendChild($dom->createElement('title', 'Gist'));
	$gist = $body->appendChild($dom->createElement('script'));
	$gist->setAttribute('src', $url);
	exit($dom->saveHTML());
} catch (\Exception $exc) {
	http_response_code($exc->getCode());
	exit($exc->getMessage());
}
