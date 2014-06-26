<?php
	/**
	 * @author Chris Zuber <shgysk8zer0@gmail.com>
	 * @copyright 2014, Chris Zuber
	 * @license /LICENSE
	 * @package chriszuber
	 */

	require_once('./functions.php');
	config();
	$DB = _pdo::load('connect');
	$login = login::load('connect');
	$session = session::load();

	if(isset($session->logged_in) and $session->logged_in) { //Check login if session
		$login->setUser($session->user)->setPassword($session->password)->setRole($session->role)->setLogged_In($session->logged_in);
	}
	if(is_ajax()) { // If this is an ajax request, let ajax.php handle it.
		require_once('./ajax.php');
	}
	CSP();		//Do this here to avoid CSP being set on ajax requests.
?>
<!DOCTYPE HTML>
<!--[if lt IE 7]>      <html class="lt-ie9 lt-ie8 lt-ie7 no-js"> <![endif]-->
<!--[if IE 7]>         <html class="lt-ie9 lt-ie8 no-js"> <![endif]-->
<!--[if IE 8]>         <html class="lt-ie9 no-js"> <![endif]-->
<!--[if gt IE 8]><!--> <html itemscope itemtype="http://schema.org/WebPage" class="no-js" <?php if(!localhost() and false):?> manifest="files.php?file=manifest.appcache"<?php endif?>> <!--<![endif]-->
<!--<?=date('Y-m-d H:i:s')?>-->
<?php load('head');?>
<body lang="en" contextmenu="main_menu">
	<?php load('header', 'main', 'footer');?>
</body>
</html>
