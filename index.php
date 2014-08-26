<?php
	/**
	 * @author Chris Zuber <shgysk8zer0@gmail.com>
	 * @copyright 2014, Chris Zuber
	 * @license /LICENSE
	 * @package chriszuber
	 */

	require_once('./functions.php');
	config();
	define_UA();

	if(BROWSER === 'Chrome' and $_SERVER['HTTP_HOST'] === 'localhost') {
		header("Location: {$_SERVER['REQUEST_SCHEME']}://{$_SERVER['SERVER_ADDR']}{$_SERVER['REQUEST_URI']}");
	}
	$DB = _pdo::load('connect');
	$login = login::load('connect');
	$session = session::load();
	$settings = ini::load('settings');

	if(isset($session->logged_in) and $session->logged_in) { //Check login if session
		$login->setUser($session->user)->setPassword($session->password)->setRole($session->role)->setLogged_In($session->logged_in);
	}
	if(is_ajax()) { // If this is an ajax request, let ajax.php handle it.
		require_once('./ajax.php');
	}
	$pages = pages::load();
	CSP();		//Do this here to avoid CSP being set on ajax requests.
?>
<!DOCTYPE HTML>
<!--[if lt IE 7]>      <html lang="en" class="lt-ie9 lt-ie8 lt-ie7 no-js"> <![endif]-->
<!--[if IE 7]>         <html lang="en" class="lt-ie9 lt-ie8 no-js"> <![endif]-->
<!--[if IE 8]>         <html lang="en" class="lt-ie9 no-js"> <![endif]-->
<!--[if gt IE 8]><!--> <html lang="en" itemscope itemtype="http://schema.org/WebPage" class="no-js" <?php if(!localhost() and isset($settings->appcache)):?> manifest="<?=$settings->appchache?>"<?php endif?>> <!--<![endif]-->
<!--<?=date('Y-m-d H:i:s')?>-->
<?php load('head');?>
<body contextmenu="main_menu">
	<?php if(!$DB->connected) load('forms/install')?>
	<?php load('forms/login', 'header', 'main', 'footer');?>
	<dialog id="README">
		<button data-close="#README"></button><br />
		<?php load('README')?>
	</dialog>
</body>
</html>
