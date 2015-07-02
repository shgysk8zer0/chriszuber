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
if (version_compare(PHP_VERSION, getenv('MIN_PHP_VERSION'), '<')) {
	header('Content-Type: text/plain');
	http_response_code(500);
	exit('PHP version ' . getenv('MIN_PHP_VERSION') . ' or greater is required');
}

define('BASE', __DIR__);

if (PHP_SAPI === 'cli') {
	require_once __DIR__ . DIRECTORY_SEPARATOR . 'std-php-functions' . DIRECTORY_SEPARATOR . 'autoloader.php';
}
init();

if (! defined('THEME')) {
	define('THEME', 'default-theme');
}
define_UA();

set_exception_handler(new \shgysk8zer0\Core\ExceptionLog);

$redirect = false;
$URL = \shgysk8zer0\Core\URL::load();
$headers = \shgysk8zer0\Core\Headers::load();

if ($URL->host === 'localhost' and BROWSER === 'Chrome') {
	$URL->host = '127.0.0.1';
	$redirect  = true;
} elseif (substr($URL->host, 0, 4) === 'www.') {
	$URL->host = substr($URL->host, 4);
	$redirect  = true;
} elseif (array_key_exists('tags', $_REQUEST)) {
	$URL->path .= 'tags/' . urlencode($_REQUEST['tags']);
	$redirect = true;
}

if ($redirect) {
	unset($URL->user, $URL->pass, $URL->query, $URL->fragment);
	http_response_code(301);
	$headers->Location = "$URL";
	exit;
}
unset($redirect);

$session  = \shgysk8zer0\Core\Session::load();
$cookie   = \shgysk8zer0\Core\Cookies::load($URL->host);

$cookie->path     = $URL->path;
$cookie->secure   = https();
$cookie->httponly = true;


if (isset($session->logged_in) and $session->logged_in) { //Check login if session
	\shgysk8zer0\Core\Login::load()
		->setUser($session->user)
		->setPassword($session->password)
		->setRole($session->role)
		->setLogged_In($session->logged_in);
}

require_once __DIR__ . DIRECTORY_SEPARATOR . 'std-php-functions' . DIRECTORY_SEPARATOR . 'error_handler.php';
if (in_array('application/json', explode(',', $headers->accept))) {
	require_once __DIR__ . DIRECTORY_SEPARATOR . 'ajax.php';

	exit;
}
unset($URL, $login, $session, $cookie, $headers);
CSP();		//Do this here to avoid CSP being set on ajax requests.
load('html');
