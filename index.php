<?php
	/**
	 * @author Chris Zuber <shgysk8zer0@gmail.com>
	 * @package chriszuber
	 * @version 2.2
	 * @copyright 2014, Chris Zuber
	 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3 (GPL-3.0)
	 *
	 * This program is free software; you can redistribute it and/or
	 * modify it under the terms of the GNU General Public License
	 * as published by the Free Software Foundation, either version 3
	 * of the License, or (at your option) any later version.
	 *
	 * This program is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	 * GNU General Public License for more details.
	 *
	 * You should have received a copy of the GNU General Public License
	 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */
	require_once('./functions.php');
	config();
	define_UA();

	if(BROWSER === 'Chrome' and $_SERVER['HTTP_HOST'] === 'localhost') {
		header("Location: {$_SERVER['REQUEST_SCHEME']}://{$_SERVER['SERVER_ADDR']}{$_SERVER['REQUEST_URI']}");
		exit();
	}

	$DB = \core\PDO::load('connect');
	$login = \core\login::load('connect');
	$session = \core\session::load();
	$settings = \core\ini::load('settings');

	if(!defined('THEME')) {
		define('THEME', 'default');
	}

	if(isset($session->logged_in) and $session->logged_in) { //Check login if session
		$login->setUser($session->user)->setPassword($session->password)->setRole($session->role)->setLogged_In($session->logged_in);
	}
	if(is_ajax()) { // If this is an ajax request, let ajax.php handle it.
		require_once('./ajax.php');
	}
	$pages = \core\pages::load();
	CSP();		//Do this here to avoid CSP being set on ajax requests.
?>
<!DOCTYPE HTML>
<!--[if lt IE 7]>      <html lang="en" class="lt-ie9 lt-ie8 lt-ie7 no-js"> <![endif]-->
<!--[if IE 7]>         <html lang="en" class="lt-ie9 lt-ie8 no-js"> <![endif]-->
<!--[if IE 8]>         <html lang="en" class="lt-ie9 no-js"> <![endif]-->
<!--[if gt IE 8]><!--> <html lang="en" itemscope itemtype="http://schema.org/WebPage" class="no-js" <?php if(!localhost() and isset($settings->appcache)):?> manifest="<?=URL . '/' . $settings->appcache?>"<?php endif?>> <!--<![endif]-->
<!--<?=date('Y-m-d H:i:s')?>-->
<?php load('head');?>
<body contextmenu="main_menu" <?=defined('GA') ?'data-ga="' . GA . '"' : null ?>>
	<?php if(!$DB->connected) load('forms/install');?>
	<?php load('forms/login', 'header', 'main', 'footer');?>
	<span hidden><?php readfile('images/icons/combined.svg');?></span>
</body>
</html>
