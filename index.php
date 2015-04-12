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

	error_reporting(0);
	require_once __DIR__ . DIRECTORY_SEPARATOR . 'functions.php';
	init();
	define_UA();

	$exception_log = \shgysk8zer0\Core\File::load('logs/exceptions.log');
	$error_log     = \shgysk8zer0\Core\File::load('logs/errors.log');

	set_exception_handler($exception_log);

	set_error_handler(function(
		$level,
		$message,
		$file,
		$line,
		array $context = array()
	) use ($error_log)
	{
		try {
			throw new \ErrorException($message, 0, $level, $file, $line);
		} catch (\ErrorException $e) {
			$error_log($e . PHP_EOL);
		}
	}, E_ALL);


	$redirect = false;

	$URL = \shgysk8zer0\Core\URL::load();

	if (! https() and in_array('mod_ssl', apache_get_modules())) {
		$URL->scheme = 'https';
		$redirect    = true;
	}

	if ($URL->host === 'localhost' and BROWSER === 'Chrome') {
		$URL->host = '127.0.0.1';
		$redirect  = true;
	} elseif (preg_match('/^www\./', $URL->host)) {
		$URL->host = preg_replace('/^www\./', null, $URL->host);
		$redirect  = true;
	}

	if ($redirect) {
		http_response_code(301);
		header("Location: $URL");
		exit();
	}
	unset($redirect);

	if (version_compare(PHP_VERSION, getenv('MIN_PHP_VERSION'), '<')) {
		header('Content-Type: text/plain');
		http_response_code(500);
		exit('PHP version ' . getenv('MIN_PHP_VERSION') . ' or greater is required');
	}

	$DB       = \shgysk8zer0\Core\PDO::load('connect.json');
	$login    = \shgysk8zer0\Core\Login::load();
	$session  = \shgysk8zer0\Core\Session::load();
	$settings = \shgysk8zer0\Core\Resources\Parser::parseFile('settings.json');
	$cookie   = \shgysk8zer0\Core\Cookies::load($URL->host);

	$cookie->path     = $URL->path;
	$cookie->secure   = https();
	$cookie->httponly = true;

	if (! defined('THEME')) {
		define('THEME', 'default');
	}

	if (isset($session->logged_in) and $session->logged_in) { //Check login if session
		$login
			->setUser($session->user)
			->setPassword($session->password)
			->setRole($session->role)
			->setLogged_In($session->logged_in);
	}
	if (is_ajax()) { // If this is an ajax request, let ajax.php handle it.
		require_once __DIR__ . DIRECTORY_SEPARATOR . 'ajax.php';
	}
	$pages = \shgysk8zer0\Core\Pages::load();
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
	<?php
		if(! $DB->connected) {
			load('forms/install');
		}
		load('forms/login', 'header', 'main', 'footer');
	?>
</body>
</html>
