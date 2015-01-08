<?php
	$storage = \shgysk8zer0\Core\storage::load();

	if($DB->connected) {
		$head = $DB->name_value('head');
	}

	else {
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
		$pages = \shgysk8zer0\Core\pages::load();
	}
	else {
		$pages = new \stdClass();
		$pages->title = null;
		$pages->rss = null;
	}
?>
<head>
<meta charset="<?=$head->charset?>"/>
<title><?=(!(isset($pages) and is_string($pages->title) and strlen($pages->title)) or $pages->title === TITLE) ? TITLE : "{$pages->title} | " . TITLE ?></title>
<base href="<?=URL?>/"/>
<!--=====================Standard meta tags==================================-->
<meta name="description" content="<?=isset($pages->description) ? $pages->description : $head->description?>"/>
<meta name="keywords" content="<?=isset($pages->keywords) ? $pages->keywords : $head->keywords?>"/>
<meta name="robots" content="<?=$head->robots?>"/>
<meta name="author" content="<?=$head->author?>"/>
<!--==============================Schema.org=================================-->
<meta itemprop="name" content="<?=(is_null($pages->title) or $pages->title === TITLE) ? TITLE : "{$pages->title} | " . TITLE ?>"/>
<meta itemprop="url" content="<?=URL . $_SERVER['REQUEST_URI'];?>"/>
<meta itemprop="description" content="<?=isset($pages->description) ? $pages->description : $head->description?>"/>
<meta itemprop="keywords" content="<?=isset($pagse->keywords) ? $pages->keywords : $head->keywords?>"/>
<meta itemprop="image" content="<?=URL?>/super-user.png"/>
<!--======================Twitter meta tags==================================-->
<meta name="twitter:card" content="summary"/>
<meta name="twitter:site" content="@shgysk8zer0"/>
<!--=====================Facebook meta tags==================================-->
<meta property="og:title" content="<?=(is_null($pages->title) or $pages->title === TITLE) ? TITLE : "{$pages->title} | " . TITLE ?>"/>
<meta property="og:site_name" content="<?=TITLE;?>"/>
<meta property="og:url" content="<?=URL . $_SERVER['REQUEST_URI'];?>"/>
<meta property="og:description" content="<?=isset($pages->description) ? $pages->description : $head->description?>"/>
<meta property="og:image" content="<?=URL?>/super-user.png"/>
<meta property="og:type" content="website"/>
<meta property="og:locale" content="en_us"/>
<!--=========================================================================-->
<meta name="viewport" content="<?=$head->viewport?>"/>
<meta name="mobile-web-app-capable" content="yes">
<link rel="canonical" href="<?=preg_replace('/^http(s)?' . preg_quote('://', '/')  .'(www\.)?/', 'http://', URL) . $_SERVER['REQUEST_URI']?>"/>
<link rel="shortcut icon" type="image/x-icon" href="favicon.ico"/>
<link rel="icon" type="image/svg" sizes="any" href="favicon.svgz?t=<?=time()?>"/>
<link rel="alternate icon" type="image/png" sizes="16x16" href="favicon.png"/>
<?php if(localhost() and BROWSER === 'Firefox'):?>
	<link rel="stylesheet" type="text/css" href="stylesheets/<?=THEME?>/import.css" media="all"/>
<?php else:?>
	<link rel="stylesheet" type="text/css" href="stylesheets/<?=THEME?>/output.css" media="all"/>
<?php endif?>
<?php if(isset($head->rss)):?>
<link href="<?=$head->rss?>" rel="alternate" type="application/rss+xml" title="<?=$head->title?> RSS Feed" />
<?php endif?>
<?php if(isset($head->publisher)):?><link rel="publisher" href="https://plus.google.com/<?=$head->publisher?>"><?php endif?>
<!--[if IE]>
<script type="text/javascript" src="scripts/polyfills.js"></script>
<![endif]-->
<?php if(localhost()):?>
	<?php if(BROWSER === 'Firefox'):?>
		<script type="application/javascript;version=1.8" src="scripts/functions.js" async></script>
		<script type="application/javascript;version=1.8" src="scripts/custom.js" async></script>
	<?php else:?>
		<script type="application/javascript" src="scripts/functions.js" async></script>
		<script type="application/javascript" src="scripts/custom.js" defer></script>
	<?php endif?>
<?php else:?>
	<?php if(BROWSER === 'Firefox'):?>
		<script type="application/javascript;version=1.8" src="scripts/combined.js" async></script>
	<?php else:?>
		<script type="application/javascript" src="scripts/combined.js" async></script>
	<?php endif?>
<?php endif?>
<?php if(!localhost() and isset($head->google_analytics_code) and !DNT()): define('GA', $head->google_analytics_code)?>
	<script type="application/javascript" src="scripts/analytics.js" async defer></script>
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
