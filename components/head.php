<?php
	$storage = storage::load();
	$head = $DB->name_value('head');
	$storage->site_info = $head;
	define_UA();						// Firefox handles JavaScript versions, whereas Chrome does not.
	global $site;
?>
<head>
<meta charset="<?=$head->charset?>"/>
<title><?=$head->title?></title>
<base href="<?=URL?>/"/>
<meta name="description" content="<?=$head->description?>"/>
<meta name="keywords" content="<?=$head->keywords?>"/>
<meta name="robots" content="<?=$head->robots?>"/>
<meta name="author" content="<?=$head->author?>"/>
<meta itemprop="name" content="<?=$head->title?>"/>
<meta itemprop="description" content="<?=$head->description?>"/>
<meta itemprop="keywords" content="<?=$head->keywords?>"/>
<meta itemprop="author" content="<?=$head->author?>"/>
<meta itemprop="image" content="favicon.svgz"/>
<meta name="viewport" content="<?=$head->viewport?>"/>
<meta name="mobile-web-app-capable" content="yes">
<link rel="cannonical" href="<?=URL?>"/>
<link rel="icon" type="image/svg" sizes="any" href="favicon.svgz?t=<?=time()?>"/>
<link rel="alternate icon" type="image/png" sizes="16x16" href="favicon.png"/>
<link rel="stylesheet" type="text/css" href="stylesheets/normalize.css" media="all"/>
<link rel="stylesheet" type="text/css" href="stylesheets/style.css" media="all"/>
<link rel="stylesheet" type="text/css" href="stylesheets/fonts.css" media="all"/>
<link rel="stylesheet" type="text/css" href="stylesheets/animations.css" media="screen"/>
<?php if($site['debug']):?>
	<script type="application/javascript" src="scripts/polyfills.js"></script>
	<script type="application/javascript" src="scripts/promises.js"></script>
	<?php if(BROWSER === 'Firefox'):?>
		<script type="application/javascript;version=1.8" src="scripts/functions.js" async></script>
		<script type="application/javascript;version=1.8" src="scripts/custom.js" defer></script>
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
<!--[if lte IE 8]>
<script type="text/javascript">
	var html5=new Array('header','hgroup','nav','menu','main','section','article','footer','aside','mark');
	for(var i=0;i<html5.length;i++){document.createElement(html5[i]);}
</script>
<![endif]-->
</head>
