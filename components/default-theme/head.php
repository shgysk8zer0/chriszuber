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
?>
<head>
<title><?=(!(isset($pages) and is_string($pages->title) and strlen($pages->title)) or $pages->title === TITLE) ? TITLE : "{$pages->title} | " . TITLE ?></title>
<base href="<?=URL?>"/>
<meta charset="<?=$head->charset?>"/>
<meta name="referrer" content="origin"/>
<!--=====================Standard meta tags==================================-->
<meta name="description" content="<?=isset($pages->description) ? $pages->description : $head->description?>"/>
<meta name="keywords" content="<?=isset($pages->keywords) ? $pages->keywords : $head->keywords?>"/>
<meta name="robots" content="<?=$head->robots?>"/>
<meta name="author" content="<?=$head->author?>"/>
<!--==============================Schema.org=================================-->
<meta itemprop="name" content="<?=(is_null($pages->title) or $pages->title === TITLE) ? TITLE : "{$pages->title} | " . TITLE ?>"/>
<meta itemprop="url" content="<?=$canonical?>"/>
<meta itemprop="description" content="<?=isset($pages->description) ? $pages->description : $head->description?>"/>
<meta itemprop="keywords" content="<?=isset($pagse->keywords) ? $pages->keywords : $head->keywords?>"/>
<meta itemprop="image" content="<?=URL?>super-user.png"/>
<!--======================Twitter meta tags==================================-->
<meta name="twitter:card" content="summary"/>
<meta name="twitter:site" content="@shgysk8zer0"/>
<!--=====================Facebook meta tags==================================-->
<meta property="og:title" content="<?=(is_null($pages->title) or $pages->title === TITLE) ? TITLE : "{$pages->title} | " . TITLE ?>"/>
<meta property="og:site_name" content="<?=TITLE;?>"/>
<meta property="og:url" content="<?=$canonical?>"/>
<meta property="og:description" content="<?=isset($pages->description) ? $pages->description : $head->description?>"/>
<meta property="og:image" content="<?=URL?>super-user.png"/>
<meta property="og:type" content="website"/>
<meta property="og:locale" content="en_us"/>
<!--=========================================================================-->
<meta name="viewport" content="<?=$head->viewport?>"/>
<meta name="mobile-web-app-capable" content="yes">
<link rel="canonical" href="<?=$canonical?>"/>
<link rel="shortcut icon" type="image/x-icon" href="favicon.ico"/>
<link rel="icon" type="image/svg" sizes="any" href="favicon.svgz?t=<?=time()?>"/>
<link rel="alternate icon" type="image/png" sizes="16x16" href="favicon.png"/>
<link rel="search" type="application/opensearchdescription+xml" title="<?=TITLE?> Tag Search" href="<?=URL?>opensearch.php"/>
<link rel="prefetch" href="images/icons/combined.svg" type="image/svg+xml"/>
<link rel="stylesheet" type="text/css" href="stylesheets/<?=THEME?>/<?=(localhost() and BROWSER === 'Firefox') ? 'import' : 'output'?>.css" media="screen"/>
<link rel="stylesheet" type="text/css" href="stylesheets/core-css/print.css" media="print"/>
<?php if(isset($head->rss)):?>
<link href="<?=$head->rss?>" rel="alternate" type="application/rss+xml" title="<?=$head->title?> RSS Feed" />
<?php endif?>
<?php if(isset($head->publisher)):?><link rel="publisher" href="https://plus.google.com/<?=$head->publisher?>"><?php endif?>
<?php if (BROWSER === 'IE'):?>
<script type="text/javascript" src="scripts/std-js/polyfills.js"></script>
<?php endif;?>
<?php
	if (localhost()): {
		foreach (get_dev_scripts() as $script) {
			echo call_user_func_array('mk_script_tag', $script) . PHP_EOL;
		}
		unset($script);
	}
else:?>
	<script type="<?=(BROWSER === 'Firefox') ? 'application/javascript;version=1.8' : 'application/javascript'?>" src="scripts/combined.js" async></script>
<?php endif?>
<?php if(! localhost() and isset($head->google_analytics_code) and ! DNT()): define('GA', $head->google_analytics_code)?>
	<script type="application/javascript" src="scripts/std-js/analytics.js" async defer></script>
<?php else:?>
	<!--Analytics not used to honor Do Not Track Header-->
<?php endif?>
<!--[if lte IE 8]>
<script type="text/javascript">
	var html5=new Array('header','hgroup','nav','menu','main','section','article','footer','aside','mark', 'details', 'summary', 'dialog', 'figure', 'figcaption', 'picture', 'source');
	for(var i=0;i<html5.length;i++){document.createElement(html5[i]);}
</script>
<![endif]-->
</head>
<?php ob_flush(); flush();?>
