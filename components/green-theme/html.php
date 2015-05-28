<!DOCTYPE HTML>
<!--[if lt IE 7]>      <html lang="en" class="lt-ie9 lt-ie8 lt-ie7 no-js"> <![endif]-->
<!--[if IE 7]>         <html lang="en" class="lt-ie9 lt-ie8 no-js"> <![endif]-->
<!--[if IE 8]>         <html lang="en" class="lt-ie9 no-js"> <![endif]-->
<!--[if gt IE 8]><!--> <html lang="en" itemscope itemtype="http://schema.org/WebPage" class="no-js" <?php if(! localhost() and isset($settings->appcache)):?> manifest="<?=URL . '/' . $settings->appcache?>"<?php endif?>> <!--<![endif]-->
<!--<?=date('Y-m-d H:i:s')?>-->
<?php load('head');?>
<body contextmenu="main_menu" <?=defined('GA') ?'data-ga="' . GA . '"' : null ?>>
	<?php
		if(! $DB->connected) {
			load('forms/install');
		}
		load('forms/login', 'header', 'main', 'footer');
	?>
</body>
</html>
