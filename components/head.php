<?php
	$head = $DB->name_value('head');
?>
<head>
<meta charset="<?=$head->charset?>"/>
<title><?=$head->title?></title>
<meta name="description" content="<?=$head->description?>"/>
<meta name="keywords" content="<?=$head->keywords?>"/>
<meta name="robots" content="<?=$head->robots?>"/>
<meta name="author" content="<?=$head->author?>"/>
<meta itemprop="name" content="<?=$head->title?>"/>
<meta itemprop="description" content="<?=$head->description?>"/>
<meta itemprop="keywords" content="<?=$head->keywords?>"/>
<meta itemprop="author" content="<?=$head->author?>"/>
<meta itemprop="image" content="<?=URL?>/favicon.svgz"/>
<meta name="viewport" content="<?=$head->viewport?>"/>
<meta name="mobile-web-app-capable" content="yes">
<link rel="icon" type="image/svg" sizes="any" href="<?=URL?>/favicon.svgz?t=<?=time()?>"/>
<link rel="alternate icon" type="image/png" sizes="16x16" href="<?=URL?>/favicon.png"/>
<link rel="stylesheet" type="text/css" href="<?=URL?>/stylesheets/normalize.css" media="all"/>
<link rel="stylesheet" type="text/css" href="<?=URL?>/stylesheets/style.css" media="all"/>
<link rel="stylesheet" type="text/css" href="<?=URL?>/stylesheets/fonts.css" media="all"/>
<link rel="stylesheet" type="text/css" href="<?=URL?>/stylesheets/animations.css" media="screen"/>
<link rel="stylesheet" type="text/css" href="<?=URL?>/stylesheets/mobile.css" media="phone, screen and (max-width:499px)"/>
<script type="text/javascript" src="<?=URL?>/scripts/functions.js" async></script>
<script type="text/javascript" src="<?=URL?>/scripts/polyfill.js" async></script>
<script type="text/javascript" src="<?=URL?>/scripts/custom.js" defer></script>
<!--[if lte IE 8]>
<script type="text/javascript">
	var html5=new Array('header','hgroup','nav','menu','main','section','article','footer','aside','mark');
	for(var i=0;i<html5.length;i++){document.createElement(html5[i]);}
</script>
<![endif]-->
</head>
