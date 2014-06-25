<?php
	$storage = storage::load();
	$head = $DB->name_value('head');
	$storage->site_info = $head;
	define_UA();						// Firefox handles JavaScript versions, whereas Chrome does not.
	$connect = ini::load('connect');
	$pages = pages::load();

	$page = ($pages->description) ? $pages : $head;
?>
<head>
<meta charset="<?=$head->charset?>"/>
<title><?=$head->title?></title>
<base href="<?=URL?>/"/>
<meta name="description" content="<?=$page->description?>"/>
<meta name="keywords" content="<?=$page->keywords?>"/>
<meta name="robots" content="<?=$head->robots?>"/>
<meta name="author" content="<?=$page->author?>"/>
<meta itemprop="name" content="<?=$head->title?>"/>
<meta itemprop="url" content="<?=URL . $_SERVER['REQUEST_URI']?>"/>
<meta itemprop="description" content="<?=$page->description?>"/>
<meta itemprop="keywords" content="<?=$page->keywords?>"/>
<meta itemprop="author" content="<?=$page->author?>"/>
<meta itemprop="image" content="<?=URL?>/super-user.png"/>
<meta name="viewport" content="<?=$head->viewport?>"/>
<meta name="mobile-web-app-capable" content="yes">
<!--<link rel="canonical" itemprop="url" href="<?=preg_replace('/^http(s)?' . preg_quote('://', '/')  .'(www\.)?/', 'http://', URL) . $_SERVER['REQUEST_URI']?>"/>-->
<link rel="canonical" itemprop="url" href="http://chriszuber.com<?=$_SERVER['REQUEST_URI']?>"/>
<link rel="favorite icon" type="image/x-icon" href="favicon.ico"/>
<link rel="icon" type="image/svg" sizes="any" href="favicon.svgz?t=<?=time()?>"/>
<link rel="alternate icon" type="image/png" sizes="16x16" href="favicon.png"/>
<link rel="stylesheet" type="text/css" href="stylesheets/normalize.css" media="all"/>
<link rel="stylesheet" type="text/css" href="stylesheets/style.css" media="all"/>
<link rel="stylesheet" type="text/css" href="stylesheets/fonts.css" media="all"/>
<link rel="stylesheet" type="text/css" href="stylesheets/animations.css" media="screen"/>
<link href="<?=URL?>/feed.rss" rel="alternate" type="application/rss+xml" title="<?=$head->title?> RSS Feed" />
<?php if(isset($head->publisher)):?><link rel="publisher" href="https://plus.google.com/<?=$head->publisher?>"><?php endif?>
<?php if($connect->debug):?>
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
	<script type="application/javascript" nonce="<?=$_SESSION['nonce']?>">
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
