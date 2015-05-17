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

	define('BASE', __DIR__);
	if (PHP_SAPI === 'cli') {
		require_once __DIR__ . DIRECTORY_SEPARATOR . 'std-php-functions' . DIRECTORY_SEPARATOR . 'autoloader.php';
	}
	init();
	define_UA();

	set_exception_handler(\shgysk8zer0\Core\File::load('logs/exceptions.log'));

	$redirect = false;

	$URL = \shgysk8zer0\Core\URL::load();

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

	require_once __DIR__ . DIRECTORY_SEPARATOR . 'std-php-functions' . DIRECTORY_SEPARATOR . 'error_handler.php';

	if (is_ajax()) { // If this is an ajax request, let ajax.php handle it.
		require_once __DIR__ . DIRECTORY_SEPARATOR . 'ajax.php';
		exit;
	}
	$pages = \shgysk8zer0\Pages::load();
	CSP();		//Do this here to avoid CSP being set on ajax requests.
	echo '<!DOCTYPE HTML>' . PHP_EOL;

	if (BROWSER === 'IE'): {
		echo join(PHP_EOL, array(
			new \shgysk8zer0\Core\IEConditionalComment(7, 'lt', false, '<html lang="en" class="lt-ie9 lt-ie8 lt-ie7 no-js">'),
			new \shgysk8zer0\Core\IEConditionalComment(7, null, false, '<html lang="en" class="lt-ie9 lt-ie8 no-js">'),
			new \shgysk8zer0\Core\IEConditionalComment(8, null, false, '<html lang="en" class="lt-ie9 no-js">'),
			new \shgysk8zer0\Core\IEConditionalComment(8, 'gt', false, '<html lang="en" class="no-js">')
		));
	} else: { ?>
<html lang="en" itemscope itemtype="http://schema.org/WebPage" class="no-js" <?php if(! localhost() and isset($settings->appcache)):?> manifest="<?=URL . '/' . $settings->appcache?>"<?php endif?>>
<?php
	} endif;
	load('head');
?>
<body contextmenu="main_menu" <?=defined('GA') ?'data-ga="' . GA . '"' : null ?>>
	<?php
		if(! $DB->connected) {
			load('forms/install');
		}

		load('forms/login', 'header', 'main', 'footer');
	?>
</body>
</html>
