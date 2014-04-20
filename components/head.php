<?php
	$head = $DB->name_value('head');
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
<link rel="icon" type="image/svg" sizes="any" href="favicon.svgz?t=<?=time()?>"/>
<link rel="alternate icon" type="image/png" sizes="16x16" href="favicon.png"/>
<link rel="stylesheet" type="text/css" href="stylesheets/normalize.css" media="all"/>
<link rel="stylesheet" type="text/css" href="stylesheets/style.css" media="all"/>
<link rel="stylesheet" type="text/css" href="stylesheets/fonts.css" media="all"/>
<link rel="stylesheet" type="text/css" href="stylesheets/animations.css" media="screen"/>
<script type="text/javascript" src="scripts/functions.js;version=1.7" async></script>
<script type="text/javascript" src="scripts/polyfill.js" async></script>
<script type="text/javascript" src="scripts/custom.js" defer></script>
<!--[if lte IE 8]>
<script type="text/javascript">
	var html5=new Array('header','hgroup','nav','menu','main','section','article','footer','aside','mark');
	for(var i=0;i<html5.length;i++){document.createElement(html5[i]);}
</script>
<![endif]-->
</head>
