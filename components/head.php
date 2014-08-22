<?php
	$storage = storage::load();

	if($DB->connected) {
		$head = $DB->name_value('head');
	}

	else {
		$head = new stdClass();
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
		$pages = pages::load();
	}
	else {
		$pages = new stdClass();
		$pages->title = null;
		$pages->rss = null;
	}
?>
<head>
<meta charset="<?=$head->charset?>"/>
<title><?=(!(isset($pages) and is_string($pages->title) and strlen($pages->title)) or $pages->title === TITLE) ? TITLE : "{$pages->title} | " . TITLE ?></title>
<base href="<?=URL?>/"/>
<meta name="description" content="<?=isset($pages->description) ? $pages->description : $head->description?>"/>
<meta name="keywords" content="<?=isset($pages->keywords) ? $pages->keywords : $head->keywords?>"/>
<meta name="robots" content="<?=$head->robots?>"/>
<meta name="author" content="<?=$head->author?>"/>
<meta itemprop="name" content="<?=(is_null($pages->title) or $pages->title === TITLE) ? TITLE : "{$pages->title} | " . TITLE ?>"/>
<meta itemprop="url" content="<?=URL . $_SERVER['REQUEST_URI']?>"/>
<meta itemprop="description" content="<?=isset($pages->description) ? $pages->description : $head->description?>"/>
<meta itemprop="keywords" content="<?=isset($pagse->keywords) ? $pages->keywords : $head->keywords?>"/>
<meta itemprop="image" content="<?=URL?>/super-user.png"/>
<meta name="viewport" content="<?=$head->viewport?>"/>
<meta name="mobile-web-app-capable" content="yes">
<link rel="canonical" href="<?=preg_replace('/^http(s)?' . preg_quote('://', '/')  .'(www\.)?/', 'http://', URL) . $_SERVER['REQUEST_URI']?>"/>
<link rel="shortcut icon" type="image/x-icon" href="favicon.ico"/>
<link rel="icon" type="image/svg" sizes="any" href="favicon.svgz?t=<?=time()?>"/>
<link rel="alternate icon" type="image/png" sizes="16x16" href="favicon.png"/>
<?php if(localhost()):?>
	<link rel="stylesheet" type="text/css" href="stylesheets/normalize.css" media="all"/>
	<?php if(BROWSER === 'Firefox'):?>
	<link rel="stylesheet" type="text/css" href="stylesheets/style.css" media="all"/>
	<?php else:?>
	<link rel="stylesheet" type="text/css" href="stylesheets/style.out.css" media="all"/>
	<?php endif?>
	<link rel="stylesheet" type="text/css" href="stylesheets/fonts.css" media="all"/>
	<link rel="stylesheet" type="text/css" href="stylesheets/animations.css" media="screen"/>
<?php else:?>
	<link rel="stylesheet" type="text/css" href="stylesheets/combined.out.css" media="all"/>
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
<?php if(!localhost() and isset($head->google_analytics_code)):?>
	<script type="application/javascript" nonce="<?=$session->nonce?>">
		<?=preg_replace('/' . preg_quote('%GOOGLE_ANALYTICS_CODE%', '/') .'/', $head->google_analytics_code, file_get_contents(BASE . '/scripts/analytics.js'))?>
	</script>
<?php endif?>
<!--[if lte IE 8]>
<script type="text/javascript">
	var html5=new Array('header','hgroup','nav','menu','main','section','article','footer','aside','mark', 'details', 'summary', 'dialog', 'figure', 'figcaption', 'picture', 'source');
	for(var i=0;i<html5.length;i++){document.createElement(html5[i]);}
</script>
<![endif]-->
</head>
