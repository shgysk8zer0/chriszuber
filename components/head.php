<?php
	$storage = storage::load();
	$head = $DB->name_value('head');
	define('TITLE', $head->title);
	$storage->site_info = $head;
	$pages = pages::load();

	$page = ($pages->description) ? $pages : $head;
?>
<head>
<meta charset="<?=$head->charset?>"/>
<title><?=($pages->head === TITLE) ? TITLE : "{$pages->title} | " . TITLE ?></title>
<base href="<?=URL?>/"/>
<meta name="description" content="<?=$page->description?>"/>
<meta name="keywords" content="<?=$page->keywords?>"/>
<meta name="robots" content="<?=$head->robots?>"/>
<meta name="author" content="<?=$page->author?>"/>
<meta itemprop="name" content="<?=($pages->head === TITLE) ? TITLE : "{$pages->title} | " . TITLE ?>"/>
<meta itemprop="url" content="<?=URL . $_SERVER['REQUEST_URI']?>"/>
<meta itemprop="description" content="<?=$page->description?>"/>
<meta itemprop="keywords" content="<?=$page->keywords?>"/>
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
<link href="<?=$head->rss?>" rel="alternate" type="application/rss+xml" title="<?=$head->title?> RSS Feed" />
<?php if(isset($head->publisher)):?><link rel="publisher" href="https://plus.google.com/<?=$head->publisher?>"><?php endif?>
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
	var html5=new Array('header','hgroup','nav','menu','main','section','article','footer','aside','mark');
	for(var i=0;i<html5.length;i++){document.createElement(html5[i]);}
</script>
<![endif]-->
</head>
