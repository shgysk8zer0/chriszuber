<?php
$storage = \shgysk8zer0\Core\Storage::load();

if ($DB->connected) {
	$head = $DB->nameValue('head');
} else {
	$head = new \stdClass();
	$head->title = 'Lorem Ipsum';
	$head->charset = 'utf-8';
	$head->description = 'Default description for the blog';
	$head->keywords = 'super, special, keywords';
	$head->author = 'Clark Kent';
	$head->robots = 'nofollow, noindex';
	$head->viewport = 'width=device-width, height=device-height';
}

define('TITLE', $head->title);

$storage->site_info = $head;

if($DB->connected) {
	$pages = \shgysk8zer0\Pages::load();
} else {
	$pages = new \stdClass();
	$pages->title = null;
	$pages->rss = null;
}

$canonical = new \shgysk8zer0\Core\URL("//{$_SERVER['SERVER_NAME']}");

if (in_array('mod_ssl', apache_get_modules())) {
	$canonical->scheme = 'https';
} else {
	$canonical->scheme = 'http';
}
$head_el = new \shgysk8zer0\Core\HTML_El('head', null, null, true);

if (!@is_object($pages) or ! isset($pages->title) or $pages->title === TITLE) {
	$head_el->title = TITLE;
} else {
	$head_el->title = $pages->title . ' | ' . TITLE;
}


$head_el
	->base(['@href' => rtrim(URL, '/') . '/'])
	->meta(['@charset' => $head->charset])
	->meta(['@name' => 'referrer', '@content' => 'origin'])
	->meta(['@name' => 'description', '@content' => isset($pages->description) ? $pages->description : $head->description])
	->meta(['@name' => 'keywords', '@content' => isset($pages->keywords) ? $pages->keywords : $head->keywords])
	->meta(['@name' => 'robots', '@content' => $head->robots])
	->meta(['@name' => 'author', '@content' => $head->author])
	->meta(['@itemprop' => 'name', '@content' => (is_null($pages->title) or $pages->title === TITLE) ? TITLE : "{$pages->title} | " . TITLE])
	->meta(['@itemprop' => 'url', '@content' => $canonical])
	->meta(['@itemprop' => 'description', '@content' => isset($pages->description) ? $pages->description : $head->description])
	->meta(['@itemprop' => 'keywords', '@content' => isset($pagse->keywords) ? $pages->keywords : $head->keywords])
	->meta(['@itemprop' => 'image', '@content' => rtrim(URL, '/') . '/super-user.png'])
	->meta(['@name' => 'twitter:card', '@content' => 'summary'])
	->meta(['@name' => 'twitter:site', '@content' => 'shgysk8zer0'])
	->meta(['@property' => 'og:title', '@content' => (is_null($pages->title) or $pages->title === TITLE) ? TITLE : "{$pages->title} | " . TITLE])
	->meta(['@property' => 'og:site_name', '@content' => TITLE])
	->meta(['@property' => 'og:url', '@content' => $canonical])
	->meta(['@property' => 'og:description', '@content' => isset($pages->description) ? $pages->description : $head->description])
	->meta(['@property' => 'og:image', '@content' => rtrim(URL, '/') . '/super-user.png'])
	->meta(['@property' => 'og:locale', '@content' => 'en_us'])
	->meta(['@name' => 'viewport', '@content' => $head->viewport])
	->meta(['@name' => 'mobile-web-app-capable', '@content' => 'yes'])
	->link(['@rel' => 'canonical', '@href' => $canonical])
	->link(['@rel' => 'shortcut icon', '@type' => 'image/x-icon', '@href' => 'favicon.ico'])
	->link(['@rel' => 'icon', '@type' => 'image/svg+xml', '@sizes' => 'any', '@href' => 'favicon.svgz?t=' . time()])
	->link(['@rel' => 'alternate icon', '@type' => 'image/png', '@sizes' => '16x16', '@href' => 'favicon.png'])
	->link(['@rel' => 'search', '@type' => 'application/opensearchdescription+xml', '@title' => TITLE . ' Tag Search', '@href' => rtrim(URL, '/') . 'opensearch.php']);
if (localhost() and BROWSER === 'Firefox') {
	$head_el->link = ['@rel' => 'stylesheet', '@type' => 'text/css', '@href' => 'stylesheets/' . THEME . '/import.css'];
} else {
	$head_el->link = ['@rel' => 'stylesheet', '@type' => 'text/css', '@href' => 'stylesheets/' . THEME . '/output.css'];
}

if (isset($head->rss)) {
	$head_el->link = ['@rel' => 'alternate', '@type' => 'application/rss+xml', '@title' => "{$head->title} RSS Feed", '@href' => $head->rss];
}

if (isset($head->publisher)) {
	$head_el->link = ['@rel' => 'publisher', '@href' => "https://plus.google.com/{$head->publisher}"];
}

if (localhost()) {
	if (BROWSER === 'Firefox') {
		$head_el->script = ['@type' => 'text/javascript;version=1.8', '@src' => 'scripts/std-js/functions.js', '@async' => 'null'];
		$head_el->script = ['@type' => 'text/javascript;version=1.8', '@src' => 'scripts/custom.js', '@async' => 'null'];
	} else {
		$head_el->script = ['@type' => 'text/javascript', '@src' => 'scripts/std-js/functions.js', '@async' => 'null'];
		$head_el->script = ['@type' => 'text/javascript', '@src' => 'scripts/custom.js', '@async' => 'null'];
	}
} else {
	if (BROWSER === 'Firefox') {
		$head_el->script = ['@type' => 'text/javascript;version=1.8', '@src' => 'scripts/combined.js', '@async' => 'null'];
	} else {
		$head_el->script = ['@type' => 'text/javascript', '@src' => 'scripts/combined.js', '@async' => 'null'];
	}
}

if (! localhost() and isset($head->google_analytics_code) and ! DNT()) {
	define('GA', $head->google_analytics_code);
	$head_el->script = ['@type' => 'text/javascript', '@src' => 'scripts/analytics.js', '@async' => 'null', '@defer' => null];
} else {
	$head_el->appendChild(new \DOMComment('Analytics not used to honor Do Not Track Header'));
}
$head_el->appendChild(new \DOMComment(
	"[if lte IE 8]>
	var html5=new Array('header','hgroup','nav','menu','main','section','article','footer','aside','mark', 'details', 'summary', 'dialog', 'figure', 'figcaption', 'picture', 'source');
	for(var i=0;i<html5.length;i++){document.createElement(html5[i]);}
	<![endif]"
));
echo $head_el;
ob_flush();
flush();
return;
