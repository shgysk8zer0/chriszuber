<?php
	/**
	 * @author Chris Zuber <shgysk8zer0@gmail.com>
	 * @copyright 2014, Chris Zuber
	 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3 (GPL-3.0)
	 * @package core_shared
	 * @version 2014-07-07
	 */

	if (!defined('PHP_VERSION_ID')) {
		$version = explode('.', PHP_VERSION);
		define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));

		if (PHP_VERSION_ID < 50207) {
			define('PHP_MAJOR_VERSION',   $version[0]);
			define('PHP_MINOR_VERSION',   $version[1]);
			define('PHP_RELEASE_VERSION', $version[2]);
		}
	}

	spl_autoload_extensions('.class.php');
	spl_autoload_register();				 //Load class by naming it

	init();

	/**
	 * Prevents functions registering from executing multiple times {Opt-in}.
	 *
	 * @param   Callable $function [Register with __FUNCTION__ magic constant]
	 * @return  boolean            [Whether or not it has been executed already]
	 * @example if(first_run(__FUNCTION__)) {...}
	 * @example if(!first_run(__FUNCTION__)) return;
	 */

	function first_run(Callable $function = null) {
		static $ran = [];
		if(array_key_exists($function, $ran)) return false;
		else {
			$ran[$function] = true;
			return true;
		}
	}

	/**
	 * Initial configuration. Setup include_path, gather database
	 * connection information, set undefined properties to
	 * default values, start a new \core\session, and set nonce
	 *
	 * @param bool $session
	 * @return array $info
	 */

	function init($session = true) {
		if(!first_run(__FUNCTION__)) return;
		//Include current directory, config/, and classes/ directories in include path
		set_include_path(__DIR__ . DIRECTORY_SEPARATOR . 'classes' . PATH_SEPARATOR . get_include_path() . PATH_SEPARATOR . __DIR__ . PATH_SEPARATOR . __DIR__  . DIRECTORY_SEPARATOR . 'config');

		if(@file_exists('./config/define.ini')) {
			foreach(parse_ini_file('./config/define.ini') as $key => $value) {
				define(strtoupper(preg_replace('/\s|-/', '_', $key)), $value);
			}
		}

		if(!defined('BASE')) define('BASE', __DIR__);
		if(PHP_SAPI == 'cli' and !defined('URL')) define('URL', 'http://localhost');
		else if(!defined('URL')) ($_SERVER['DOCUMENT_ROOT'] === __DIR__ . DIRECTORY_SEPARATOR or $_SERVER['DOCUMENT_ROOT'] === __DIR__) ? define('URL', "${_SERVER['REQUEST_SCHEME']}://{$_SERVER['SERVER_NAME']}") : define('URL', "${_SERVER['REQUEST_SCHEME']}://{$_SERVER['SERVER_NAME']}/" . end(explode('/', BASE)));
		if($session) {
			\core\session::load();
			nonce(50);									// Set a nonce of n random characters
		}
	}

	/**
	 * Load and configure site settings
	 * Loads all files in requires directive
	 * Setup custom error handler
	 *
	 * @parmam void
	 * @return void
	 */

	function config($settings_file = 'settings') {
		if(!first_run(__FUNCTION__)) return;
		$settings = \core\ini::load((string)$settings_file);
		if(isset($settings->path)) {
			set_include_path(get_include_path() . PATH_SEPARATOR . preg_replace('/(\w)?,(\w)?/', PATH_SEPARATOR, $settings->path));
		}

		if(isset($settings->charset) and is_string($settings->charset)) {
			ini_set('default_charset', strtoupper($settings->charset));
		}
		else {
			ini_set('default_charset', 'UTF-8');
		}

		if(isset($settings->credentials_extension)) {
			\core\resources\pdo_connect::$ext = $settings->credentials_extension;
		}
		else {
			\core\resources\pdo_connect::$ext = 'ini';
		}

		if(isset($settings->requires)) {
			foreach(explode(',', $settings->requires) as $file) {
				require_once(__DIR__ . DIRECTORY_SEPARATOR . trim($file));
			}
		}

		if(isset($settings->time_zone)) {
			date_default_timezone_set($settings->time_zone);
		}

		if(isset($settings->autoloader)) {
			spl_autoload_register($settings->autoloader);
		}

		//Error Reporting Levels: http://us3.php.net/manual/en/errorfunc.constants.php
		if(isset($settings->error_handler) and isset($settings->debug)) {
			$error_handler = $settings->error_handler;
			if(is_string($settings->debug)) $settings->debug = strtolower($settings->debug);
			error_reporting(0);
			switch($settings->debug) {
				case 'true': case 'all': case 'on': {
					set_error_handler($error_handler, E_ALL);
				} break;

				case 'false': case 'off': {
					set_error_handler($error_handler, 0);
				} break;

				case 'core': {
					set_error_handler($error_handler, E_CORE_ERROR | E_CORE_WARNING);
				} break;

				case 'strict': {
					set_error_handler($error_handler, E_ALL^E_USER_ERROR^E_USER_WARNING^E_USER_NOTICE);
				} break;

				case 'warning': {
					set_error_handler($error_handler, E_ALL^E_STRICT^E_USER_ERROR^E_USER_WARNING^E_USER_NOTICE);
				} break;

				case 'notice': {
					set_error_handler($error_handler, E_ALL^E_STRICT^E_WARNING^E_USER_ERROR^E_USER_WARNING^E_USER_NOTICE);
				} break;

				case 'developement': {
					set_error_handler($error_handler, E_ALL^E_NOTICE^E_WARNING^E_STRICT^E_DEPRECATED);
				} break;

				case 'production': {
					set_error_handler($error_handler, E_COMPILE_ERROR|E_RECOVERABLE_ERROR|E_ERROR|E_CORE_ERROR);
				} break;

				default: {
					set_error_handler($error_handler, E_COMPILE_ERROR|E_RECOVERABLE_ERROR|E_ERROR|E_CORE_ERROR);
				}
			}
		}

		else {
			error_reporting(E_ALL);
		}
	}

	/**
	 * Default custom error handler function.
	 * Should never be used directly. Ran automatically when an error is caught.
	 *
	 * This function only passes the error details into a statically loaded class
	 *
	 * @param int $error_level (Integer version for E_FATAL, E_DEPRECIATED, etc)
	 * @param string $error_message (Error description generated by PHP)
	 * @param string $file (File in which the error occured)
	 * @param int $line (The line number on which the error occured)
	 * @param mixed $scope (All variables set in the current scope when error occured)
	 * @return mixed (boolen false will result in PHP default error handling)
	 */

	function error_reporter_class(
		$error_level = null,
		$error_message = null,
		$file = null,
		$line = null,
		$scope = null
	) {
		static $reporter = null;

		if(is_null($reporter)) {
			$settings = \core\ini::load('settings');
			$reporter = \core\error_reporter::load(
				(isset($settings->error_method)) ? $settings->error_method : 'log'
			);
			if(is_null($settings->error_method or $settings->error_method === 'log')) {
				$reporter->log = (isset($settings->error_log)) ? $settings->error_log : 'errors.log';
			}
		}

		return $reporter->report(
			(string)$error_level,
			(string)$error_message,
			(string)$file,
			(string)$line,
			$scope
		);
	}

	/**
	 * Optimized resource loading using static variables and closures
	 * Intended to minimize resource usage as well as limit scope
	 * of variables from inluce()s
	 *
	 * Similar to include(), except that it shares limited resources
	 * and does not load into the current scope for security reasons.
	 *
	 * @param mixed args
	 * @return boolean
	 * @example load(string | array[string | array[, ...]]*)
	 */

	function load() {
		static $DB, $settings, $session, $login, $cookie, $path = null;
		if(is_null($path)) {
			$DB = \core\PDO::load('connect');
			$settings = \core\resources\Parser::parse('settings.ini');
			$session = \core\session::load();
			$login = \core\login::load();
			$cookie = \core\cookies::load();

			if(defined('THEME')) {
				$path = BASE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . THEME . DIRECTORY_SEPARATOR;
			}
			else {
				$path = BASE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR;
			}
		}

		array_map(function($fname) use (
			$DB,
			$path,
			$settings,
			$session,
			$cookie,
			$login
		) {
			include $path . $fname . '.php';
		}, flatten(func_get_args()));
	}

	/**
	 * Similar to load(), except that it returns rather than prints
	 *
	 * @example(string | array[string | array[, ...]]*)
	 * @param mixed (string, arrays, ... whatever. They'll be converted to an array)
	 * @return string (results echoed from load())
	 */

	function load_results() {
		ob_start();
		load(func_get_args());
		return ob_get_clean();
	}

	/**
	 * Loads a widget as an <iframe>
	 * Sets src attribute to $src if it is a URL, otherwise sets the source
	 * to a data-uri of the contents if it is a file and readable.
	 *
	 * @link http://www.w3schools.com/tags/tag_iframe.asp
	 *
	 * @param  string $src        [filename/path or URI]
	 * @param  array  $sandbox    [Sanbox permissions]
	 * @param  array  $attributes [$name => $value | $value attributes set]
	 * @param boolean $print      [echoes if true, returns if false]
	 * @return string             [an iframe element]
	 */

	function load_widget(
		$src,
		array $sandbox = null,
		array $attributes = null,
		$print = true
	) {
		$iframe = '';
		if(is_url($src)) {
			$iframe .= ' src="'. $src . '"';
		}
		elseif(@file_exists($src) and is_readable($src)) {
			if(in_array(mime_type($src), ['application/x-php'])) {
				ob_start();
				include $src;
				$iframe .=' srcdoc="' . str_replace(
					['"', PHP_EOL, "\t"],
					['&quot;', null, null],
					ob_get_clean()
				) . '"';

				$iframe .= ' src=""';
			}
			else {
				$iframe .= ' src="'. data_uri($src) . '"';
			}
		}
		else return;

		if(is_array($sandbox)) {
			$iframe .= ' sandbox="';
			if(in_array('same-origin', $sandbox)) $iframe .= 'allow-same-origin';
			if(in_array('top-navigation', $sandbox)) $iframe .= ' allow-top-navigation';
			if(in_array('forms', $sandbox)) $iframe .= ' allow-forms';
			if(in_array('scripts', $sandbox)) $iframe .= ' allow-scripts';
			$iframe .='"';
		}

		if(is_array($attributes)) {
			foreach($attributes as $key => &$value) {
				if(is_int($key)) {
					$value = htmlspecialchars($value);
				}
				else {
					$value = htmlspecialchars($key) . '="' . htmlspecialchars($value) .'"';
				}
			}
			$iframe .= ' ' . join(' ', $attributes);
		}

		$iframe = '<iframe' . $iframe . '></iframe>';
		if($print) echo $iframe;
		else return $iframe;
	}

	/**
	 * Reads a file and returns a json_decoded object
	 *
	 * @link http://php.net/manual/en/function.json-decode.php
	 * @param string $filename
	 * @param bool $assoc
	 * @param int $depth
	 * @param int $options
	 * @return \stdClass Object
	 */

	function parse_json_file(
		$filename = null,
		$assoc = false,
		$depth = 512,
		$options = 0
	) {
		return json_decode(
			file_get_contents(
				(string)$filename  . '.json',
				true
			),
			(bool)$assoc,
			(int)$depth,
			(int)$options
		);
	}

	/**
	 * Convert a CSV file into an multi-dimensional array containing it's data
	 *
	 * If $headers is true, the first row will be considered column names
	 * and will be used as keys for an associattive array.
	 *
	 * Otherwise, it will be an indexed array
	 *
	 * @link http://php.net/manual/en/function.fgetcsv.php
	 * @param  string   $fname     [Name of file]
	 * @param  boolean  $headers   [First row of CSV file is headers?]
	 * @param  integer  $length    [Max line length. 0 is unlimited]
	 * @param  string   $delimiter [Set the field delimiter (one character only)]
	 * @param  string   $enclosure [Set the field enclosure character (one character only). ]
	 * @param  string   $escape    [Set the escape character (one character only). Defaults as a backslash. ]
	 * @return array               [CSV file parsed as an array (will be empty if file cannot be read)]
	 */

	function parse_csv_file(
		$fname = null,
		$headers = false,
		$length = 0,
		$delimiter = ',',
		$enclosure = '"',
		$escape = '\\'
	) {
		if(is_null($fname)) return [];
		$rows = [];
		$fname = realpath($fname);

		if(@is_readable($fname)) {
			$handle = fopen($fname, 'r');
			if(isset($handle)) {
				if($headers) {
					$cols = fgetcsv($handle);
					while(($row = fgetcsv($handle, $length, $delimiter, $enclosure, $escape)) !== false) {
						array_push($rows, array_combine($cols, $row));
					}
				}
				else {
					while(($row = fgetcsv($$handle, $length, $delimiter, $enclosure, $escape)) !== false) {
						array_push($rows, $row);
					}
				}
				fclose($handle);
			}
		}
		return $rows;
	}

	/**
	 * strips leading trailing and closing tags, including leading
	 * new lines, tabs, and any attributes in the tag itself.
	 *
	 * @param $html (html content to be stripping tags from)
	 * @return string (html content with leading and trailing tags removed)
	 * @example strip_enclosing_tags('<div id="some_div" ...><p>Some Content</p></div>')
	 */

	function strip_enclosing_tag($html = null) {
		return preg_replace('/^\n*\t*\<.+\>|\<\/.+\>$/', '', (string)$html);
	}

	/**
	 * Converts an array into a string of HTML tags containing
	 * the values of the array... useful for tables and lists.
	 *
	 * @param string $tag (Surrounding HTML tag)
	 * @param array $content
	 * @param array $attributes
	 * @return string
	 */

	function html_join(
		$tag,
		array $content = null,
		array $attributes = null
	) {
		$tag = preg_replace('/[^a-z]/', null, strtolower((string)$tag));
		$attributes = array_to_attributes($attributes);
		return "<{$tag} {$attributes}>" . join("</{$tag}><{$tag}>", $content) . "</{$tag}>";
	}

	/**
	 * Converts ['attr' => 'value'...] to attr="value"
	 *
	 * @param  array $attributes  [Key => value pairing of attributes]
	 * @return string
	 */

	function array_to_attributes(array $attributes = null) {
		/**
		 * Converts an array of attributes into a string
		 *
		 * @param array $attributes
		 * @return string
		 * @example
		 * array_to_attributes(['class' => 'myClass]) //returns 'class="myClass"'
		 */

		if(is_null($attributes)) return null;
		$str = '';
		foreach($attributes as $name => $value) {
			$str .= $name . '=' . htmlspecialchars($value);
		}
		return trim($str);
	}

	/**
	 * Prints out information about $data
	 * Wrapped in html comments or <pre><code>
	 *
	 * @param mixed $data[, boolean $comment]
	 * @return void
	 */

	function debug($data = null, $comment = false) {
		if(isset($comment)) {
			echo '<!--';
			print_r($data, is_ajax());
			echo '-->';
		}
		else {
			echo '<pre><code>';
			print_r($data, is_ajax());
			echo '</code></pre>';
		}
	}

	/**
	 * Check login status, and optionally role
	 *
	 * @param  tring $role  [user, admin, etc]
	 * @param  string $exit [option for action if checks do not pass]
	 *
	 * @return void
	 */

	function require_login($role = null, $exit = 'notify') {
		$login = \core\login::load();

		if(!$login->logged_in) {
			switch((string)$exit) {
				case 'notify': {
					$resp = new \core\json_response();
					$resp->notify(
						'We have a problem :(',
						'You must be logged in for that'
					)->send();
					return false;
					exit();
				}

				case 403: case '403': case 'exit': {
					http_response_code(403);
					exit();
				}

				case 'return' : {
					return false;
				}

				default: {
					http_response_code(403);
					exit();
				}
			}
		}

		elseif(isset($role)) {
			$role = strtolower((string)$role);
			$resp = new \core\json_response();
			$roles = ['new', 'user', 'admin'];

			$user_level = array_search($login->role, $roles);
			$required_level = array_search($role, $roles);

			if(!$user_level or !$required_level) {
				$resp->notify(
					'We have a problem',
					'Either your user\'s role or the required role are invalid',
					'images/icons/info.png'
				)->send();
				return false;
				exit();
			}

			elseif($required_level > $user_level) {
				$resp->notify(
					'We have a problem :(',
					"You are logged in as {$login->role} but this action requires {$role}",
					'images/icons/info.png'
				)->send();
				return false;
				exit();
			}

			else {
				return true;
			}
		}
		else {
			return true;
		}
	}

	/**
	 * A nonce is a random string used for validation.
	 * One is generated for every session, and is used to
	 * prevent such things as brute force attacks on form submission.
	 * Without checking a nonce, it becomes easier to brute force login attempts
	 *
	 * @param void
	 * @return void
	 */

	function check_nonce() {
		if(
			!(
				array_key_exists('nonce', $_POST) and
				array_key_exists('nonce', $_SESSION)
			)
			or $_POST['nonce'] !== $_SESSION['nonce']
		) {
			$resp = new \core\json_response();
			$resp->notify(
				'Something went wrong :(',
				'Your session has expired. Try again',
				'images/icons/network-server.png'
			)->error(
				"nonce not set or does not match"
			)->sessionStorage(
				'nonce',
				nonce()
			)->attributes(
				'[name=nonce]',
				'value',
				$_SESSION['nonce']
			)->send();
			exit();
		};
	}

	/**
	 * Content-Security-Policy is a set of rules given to a browser
	 * via an HTTP header, providing a list of allowable resources.
	 *
	 * If a resources is requested that is not specifically allowed
	 * in CSP, it is blocked. This prevents such things as key-loggers,
	 * adware, and other forms of malware from having any effect.
	 *
	 * @link http://www.html5rocks.com/en/tutorials/security/content-security-policy/
	 * @param void
	 * @return void
	 * @todo Use UA sniffing to only set correct header
	 */

	function CSP() {
		$CSP = '';
		$CSP_Policy = \core\resources\Parser::parse('csp.json');

		if(!is_object($CSP_Policy)) return;

		if(isset($CSP_Policy->enforce)) {
			$enforce = $CSP_Policy->enforce;
			unset($CSP_Policy->enforce);
		}
		else {
			$enforce = true;
		}

		foreach($CSP_Policy as $type => $src) {
			$CSP .= (is_array($src)) ? $type . ' ' . join(' ', $src) . ';' : "{$type} {$src};";
		}

		$CSP = str_replace('%NONCE%', $_SESSION['nonce'], $CSP);

		header(($enforce)
			? "Content-Security-Policy: {$CSP}"
			: "Content-Security-Policy-Report-Only: {$CSP}"
		);
	}

	/**
	 * Checks to see if the server is also the client.
	 *
	 * @param void
	 * @return boolean
	 */

	function localhost() {
		return ($_SERVER['REMOTE_ADDR'] === $_SERVER['SERVER_ADDR']);
	}

	/**
	 * Returns whether or not this is a secure (HTTPS) connection
	 *
	 * @param void
	 * @return boolean
	 */

	function https() {
		return (isset($_SERVER['HTTPS']) and $_SERVER['HTTPS']);
	}

	/**
	 * Checks and returns whether or not Do-Not-Track header
	 * requests that we not track the client
	 *
	 * @param void
	 * @return boolean
	 */

	function DNT() {
		return (isset($_SERVER['HTTP_DNT']) and $_SERVER['HTTP_DNT']);
	}

	/**
	* Convert an address to GPS coordinates (longitude & latitude)
	* using Google Maps API
	*
	* @param  string $Address [Postal address]
	* @return [stdClass]          [{"lat": $latitude, "lng": $longitude}]
	*/

	function address_to_gps($Address = null) {
		if(!is_string($Address)) return false;
		$request_url = "http://maps.googleapis.com/maps/api/geocode/xml?address=".urlencode($Address)."&sensor=true";
		$xml = simplexml_load_file($request_url);

		if (!empty($xml) and $xml->status == "OK") {
			return $xml->result->geometry->location;
		}
		else {
			return false;
		}
	}

	/**
	 * Checks for the custom Request-Type header sent in my ajax requests
	 *
	 * @param void
	 * @return boolean
	 */

	function is_ajax() {
		return (
			isset($_SERVER['HTTP_REQUEST_TYPE'])
			and $_SERVER['HTTP_REQUEST_TYPE'] === 'AJAX'
		);
	}

	/**
	 * Sets HTTP Content-Type header
	 * @param string $type
	 * @return void
	 */

	function header_type($type = null) {
		header('Content-Type: ' . (string)$type . PHP_EOL);
	}

	/**
	 * Defines a variety of things using the HTTP_USER_AGENT header,
	 * such as operating system and browser
	 *
	 * @param void
	 * @return void
	 */

	function define_UA() {
		if(!defined('UA')){
			if(isset($_SERVER['HTTP_USER_AGENT'])) {
				define('UA', $_SERVER['HTTP_USER_AGENT']);
				if(preg_match("/Firefox/i", UA)) define('BROWSER', 'Firefox');
				elseif(preg_match("/Chrome/i", UA)) define('BROWSER', 'Chrome');
				elseif(preg_match("/MSIE/i", UA)) define('BROWSER', 'IE');
				elseif(preg_match("/(Safari)||(AppleWebKit)/i", UA)) define('BROWSER', 'Webkit');
				elseif(preg_match("/Opera/i", UA)) define('BROWSER', 'Opera');
				else define('BROWSER', 'Unknown');
				if(preg_match("/Windows/i", UA)) define('OS', 'Windows');
				elseif(preg_match("/Ubuntu/i", UA)) define('OS', 'Ubuntu');
				elseif(preg_match("/Android/i", UA)) define('OS', 'Android');
				elseif(preg_match("/(IPhone)|(Macintosh)/i", UA)) define('OS', 'Apple');
				elseif(preg_match("/Linux/i", UA)) define('OS', 'Linux');
				else define('OS', 'Unknown');
			}
			else{
				define('BROWSER', 'Unknown');
				define('OS', 'Unknown');
			};
		}
	}

	/**
	 * Generates a random string to be used for form validation
	 *
	 * @link http://www.html5rocks.com/en/tutorials/security/content-security-policy/
	 * @param integer $length
	 * @return string
	 */

	function nonce($length = 50) {
		$length = (int)$length;
		if(array_key_exists('nonce', $_SESSION)) {
			return $_SESSION['nonce'];
		}
		//We are going to shuffle an alpha-numeric string to get random characters
		$str = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
		if(strlen($str) < $length) {
			$str .= nonce($length - strlen($str));
		}
		$_SESSION['nonce'] = $str;
		return $str;
	}

	/**
	 * Checks whether or not the current request was sent
	 * from the same domain
	 *
	 * @param void
	 * @return boolean
	 */

	function same_origin() {
		if(isset($_SERVER['HTTP_ORIGIN'])) {
			$origin = $_SERVER['HTTP_ORIGIN'];
		}
		elseif(isset($_SERVER['HTTP_REFERER'])) {
			$origin = $_SERVER['HTTP_REFERER'];
		}

		$name = '/^http(s)?' .preg_quote('://' . $_SERVER['SERVER_NAME'], '/') . '/';
		return (isset($origin) and preg_match($name, $origin));
	}

	/**
	 * @param void
	 * @return string (Directory one level below DOCUMENT_ROOT)
	 */

	function sub_root() {
		$root = trim($_SERVER['DOCUMENT_ROOT'], '/');
		$sub = preg_replace('/' . preg_quote(end(explode('/', $root))) . '/', '', $root);
		return $sub;
	}

	/**
	 * Remove from array by key and return it's value
	 *
	 * @param string $key, array $array
	 * @return array | null
	 */

	function array_remove($key = null, array &$array) {
		$key = (string)$key;
		if(array_key_exists($key, $array)) {
			$val = $array[$key];
			unset($array[$key]);
			return $val;
		}
		else return null;
	}

	/**
	 * Checks if the array that is the product
	 * of array_diff is empty or not.
	 *
	 * First, store all arguments as an array using
	 * func_get_arg() as $keys.
	 *
	 * Then, pop off the last argument as $arr, which is assumed
	 * to be the array to be searched and save it as its
	 * own variable. This will also remove it from
	 * the arguments array.
	 *
	 * Then, convert the array to its keys using $arr = array_keys($arr)
	 *
	 * Finally, compare the $keys by lopping through and checking if
	 * each $key is in $arr using in_array($key, $arr)
	 *
	 * @param string[, string, .... string] array
	 * @return boolean
	 * @example array_keys_exist(
	 *          	'red',
	 *           	'green',
	 *           	'blue',
	 *            	[
	 *          	  'red' => '#f00',
	 *          	  'green' => '#0f0',
	 *          	  'blue' => '#00f'
	 *             	]
	 *          ) // true
	 */

	function array_keys_exist() {
		$keys = func_get_args();
		$arr = array_pop($keys);
		$arr = array_keys($arr);

		foreach($keys as $key) {
			if(!in_array($key, $arr, true)) return false;
		}
		return true;
	}

	/**
	 * Tests if each value in an array is true
	 * @param  array  $arr [the array to test]
	 * @return bool        [all array values are true]
	 */

	function array_all_true(array $arr) {
		$arr = array_unique($arr);
		return (count($arr) === 1 and $arr[0] === true);
	}

	/**
	 * Convert a multi-dimensional array into a simple array
	 *
	 * Can't say that I'm entirely sure how it does what it does,
	 * only that it works
	 *
	 * @param mixed args
	 * @return array
	 */

	function flatten() {
		return iterator_to_array(new RecursiveIteratorIterator(
			new RecursiveArrayIterator(func_get_args())),FALSE);
	}

	/**
	 * Prints out an unordered list from an array
	 * @param array $array
	 * @return void
	 */

	function list_array(array $array) {
		$list = "<ul>";
		foreach($array as $key => $entry) {
			if(is_array($entry)) {
				$list .= list_array($value);
			}
			else {
				$entry = (string)$entry;
				$list .= "<li>{$key}: {$entry}</li>";
			}
		}
		$list .= "</ul>";

		return $list;
	}

	/**
	 * Checks if an array is associative array
	 * (A single index is a string)
	 *
	 * @param array $array
	 * @return bool
	 */

	function is_assoc(array $array) {
		return (bool)count(array_filter(array_keys($array), 'is_string'));
	}

	/**
	 * Checks if an array is indexed(numerical)
	 *
	 * @param array $array
	 * @return bool
	 */

	function is_indexed(array $array) {
		return (bool)count(array_filter(array_keys($array), 'is_int'));
	}

	/**
	 * Because I was tired of writing this... the ultimate point of programming, after all
	 *
	 * @param mixed $n
	 * @return boolean
	 */

	function is_a_number($n = null) {
		return preg_match('/^\d+$/', $n);
	}

	/**
	 * Opposite of previous.
	 *
	 * @param mixed $n
	 * @return boolean
	 */

	function is_not_a_number($n = null) {
		return !is_a_number($n);
	}

	/**
	 * Checks if $str validates as an email
	 *
	 * @param string $str
	 * @return bolean
	 * @link http://php.net/manual/en/filter.filters.validate.php
	 */

	function is_email($str = null) {
		return filter_var($str, FILTER_VALIDATE_EMAIL);
	}

	/**
	 * Checks if $str validates as a URL
	 *
	 * @param string $str
	 * @return bolean
	 * @link http://php.net/manual/en/filter.filters.validate.php
	 */

	function is_url($str = null) {
		return filter_var($str, FILTER_VALIDATE_URL);
	}

	/**
	 * Checks $str againts the pattern for its type
	 *
	 * @param string $str
	 * @return boolean
	 */

	function is_datetime($str) {
		return pattern_check('datetime', $str);
	}

	/**
	 * Checks $str againts the pattern for its type
	 *
	 * @param string $str
	 * @return boolean
	 */

	function is_date($date) {
		return pattern_check('date', $str);
	}

	/**
	 * Checks $str againts the pattern for its type
	 *
	 * @param string $str
	 * @return boolean
	 */

	function is_week($str) {
		return pattern_check('week', $str);
	}

	/**
	 * Checks $str againts the pattern for its type
	 *
	 * @param string $str
	 * @return boolean
	 */

	function is_time($str) {
		return pattern_check('time', $str);
	}

	/**
	 * Checks $str againts the pattern for its type
	 *
	 * @param string $str
	 * @return boolean
	 */

	function is_color($str) {
		return pattern_check('color', $str);
	}

	/**
	 * Checks $str againts the pattern $type
	 *
	 * @param string $str
	 * @param string $type
	 * @return boolean
	 */

	function pattern_check($type, $str) {
		return preg_match('/^' . pattern($type) . '$/', (string)$str);
	}

	/**
	 * Checks that each $inputs is set and matches a pattern
	 *
	 * Loops through an array of inputs, checking that
	 * it exists in $_REQUEST, and checks that $_REQUEST[$key]
	 * matches the specified pattern.
	 *
	 * @param array $inputs ([$key => $test])
	 * @param array $souce ($_POST, $_GET, $_REQUEST, [])
	 * @return mixed (null if all inputs valid, selector '[name="$key"]' of first invalid input if not)
	 * @example pattern_check(['num' => '\d', 'user' => is_email($source['user])], $source)
	 */

	function check_inputs(array $inputs, array $source = null) {
		if(is_null($source)) $source = $_REQUEST;

		foreach($inputs as $key => $test) {
			if(
				!array_key_exists($key, $source)
				or (is_bool($test) and !$test)
				or (is_string($test) and !preg_match('/^' . $test . '$/', $source[$key]))
			) {
				return "[name=\"{$key}\"]";
			}
		}
		return null;
	}

	/**
	 * Useful for pattern attributes as well as server-side input validation
	 * Must add regexp breakpoints for server-side use ['/^$pattern$/']
	 *
	 * @param string $type
	 * @return string (regexp)
	 */

	function pattern($type = null) {
		if(isset($type)) $type = (string)$type;
		switch($type) {
			case "text": {
				$pattern = "(\w+(\ )?)+";
			} break;

			case "name": {
				$pattern = "[A-Za-z]{3,30}";
			} break;

			case "password": {
				$pattern = "(?=^.{8,35}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$";
			} break;

			case "email": {
				$pattern = ".+@.+\.+[\w]+";
			} break;

			case "url": {
				$pattern = "(http[s]?://)?[\S]+\.[\S]+";
			} break;

			case "tel": {
				$pattern = "([+]?[1-9][-]?)?((\([\d]{3}\))|(\d{3}[-]?))\d{3}[-]?\d{4}";
			} break;

			case "number": {
				$pattern = "\d+(\.\d{1,})?";
			} break;

			case "color": {
				$pattern = "#?([a-fA-F0-9]{6}|[a-fA-F0-9]{3})";
			} break;

			case "date": {
				$pattern = "((((0?)[1-9])|(1[0-2]))(-|/)(((0?)[1-9])|([1-2][\d])|3[0-1])(-|/)\d{4})|(\d{4}-(((0?)[1-9])|(1[0-2]))-(((0?)[1-9])|([1-2][\d])|3[0-1]))";
			} break;

			case "time": {
				$pattern = "(([0-1]?\d)|(2[0-3])):[0-5]\d";
			} break;

			case 'datetime': {
				$pattern = '(19|20)\d{2}-(0?[1-9]|1[0-2])-(0?[1-9]|[12]\d?|3[01])T([01]\d|2[0-3])(:[0-5]\d)+';
			} break;

			case 'week': {
				$pattern = '\d{4}-W\d{2}';
			} break;

			case "credit": {
				$pattern = "\d{13,16}";
				} break;

			default: {
				$pattern = null;
			}
		}
		return $pattern;
	}

	/**
	 * Converts characters to UTF-8. Replaces special chars.
	 *
	 * @param string $string
	 * @return (string UTF-8 converted)
	 * @example utf('This & that') //Returns 'This &amp; that'
	 */

	function utf($string = null) {
		return htmlentities((string)$string, ENT_QUOTES | ENT_HTML5,"UTF-8");
	}

	/**
	 * List files in given path. Optional extension and strip extension from results
	 *
	 * @param [string $path[, string $ext[, boolean $strip_ext]]]
	 * @return array
	 */

	function ls($path = null, $ext = null, $strip_ext = null) {
		if(is_null($path)) $path = BASE;
		else $path = (string)$path;
		$files = array_diff(scandir($path), array('.', '..'));				// Get array of files. Remove current and previous directory (. & ..)
		$results = [];
		if(isset($ext)) {													//$ext has been passed, so let's work with it
			//$ext = (string)$ext;
			//Convert $ext into regexp
			$ext = '/' . preg_quote('.' . (string)$ext, '/') .'$/';					// Convert for use in regular expression
			if(isset($strip_ext)) {
				foreach($files as $file) {
					(preg_match($ext, $file)) ? array_push($results, preg_replace($ext, '', $file)) : null;
				}
			}
			else{
				foreach($files as $file) {
					(preg_match($ext, $file)) ? array_push($results, $file) : null;
				}
			}
			return $results;
		}
		else return $files;
	}

	/**
	 * Base 64 encode $file. Does not set data: URI
	 * @param string $file
	 * @return string (base_64 encoded)
	 */

	function encode($file = null) {
		$file = (string)$file;
		if(file_exists($file)) return base64_encode(file_get_contents($file));
	}

	/**
	 * Determine the mime-type of a file
	 * using file info or file extension
	 *
	 * @param string $file
	 * @return string (mime-type)
	 * @example mime_type(path/to/file.txt) //Returns text/plain
	 */

	function mime_type($file = null) {
		//Make an absolute path if given a relative path in $file

		$file = realpath($file);
		switch(str_replace('.', null, extension($file))){ //Start by matching file extensions
			case 'svg':
			case 'svgz': {
				$type = 'image/svg+xml';
			} break;

			case 'woff': {
				$type = 'application/font-woff';
			} break;

			case 'otf': {
				$type = 'application/x-font-opentype';
			} break;

			case 'sql': {
				$type = 'text/x-sql';
			} break;

			case 'appcache': {
				$type = 'text/cache-manifest';
			} break;

			case 'mml': {
				$type = 'application/xhtml+xml';
			} break;

			case 'ogv': {
				$type = 'video/ogg';
			} break;

			case 'webm': {
				$type = 'video/webm';
			} break;

			case 'php': {
				$type = 'application/x-php';
			} break;

			case 'ogg':
			case 'oga':
			case 'opus': {
				$type = 'audio/ogg';
			} break;

			case 'flac': {
				$type = 'audio/flac';
			} break;

			case 'm4a': {
				$type = 'audio/mp4';
			} break;

			case 'css':
			case 'cssz': {
				$type = 'text/css';
			} break;

			case 'js':
			case 'jsz': {
				$type = 'text/javascript';
			} break;

			default: {		//If not found, try the file's default
				$finfo = new \finfo(FILEINFO_MIME);
				$type = preg_replace('/\;.*$/', null, (string)$finfo->file($file));
			}
		}
		return $type;
	}

	/**
	 * Reads the contents of a file ($file) and returns
	 * the base64 encoded data-uri
	 *
	 * Useful for decreasing load times and storing resources client-side
	 *
	 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/data_URIs
	 * @param strin $file
	 * @return string (base64 encoded data-uri)
	 */

	function data_uri($file = null) {
		$file = realpath((string)$file);
		return 'data:' . mime_type($file) . ';base64,' . encode($file);
	}

	/**
	 * Returns the extension for the specified file
	 *
	 * Does not depend on whether or not the file exists.
	 * This function operates with the string, not the
	 * filesystem
	 *
	 * @param string $file
	 * @return string
	 * @example extension('path/to/file.ext') //returns '.ext'
	 */

	function extension($file = null) {
		return '.' . pathinfo((string)$file, PATHINFO_EXTENSION);
	}

	/**
	 * Returns the filename without path or extension
	 * Does not depend on whether or not the file exists.
	 * This function operates with the string, not the
	 * filesystem
	 *
	 * @param string $file
	 * @return string
	 * @example filename('/path/to/file.ext') //returns 'file'
	 */

	function filename($file = null) {
		return pathinfo((string)$file, PATHINFO_FILENAME);
	}

	/**
	* Concatonate an array of SVG files into a single SVG as <symbol>s
	*
	* If $output is given, the results will be saved to that file.
	* Otherwise, the results will be returned as a string.
	*
	* @param array  $svgs   [Array of SVG files]
	* @param string $output [Optional name of output file]
	* @link http://css-tricks.com/svg-symbol-good-choice-icons/
	*/

	function SVG_symbols(array $svgs, $output = null) {
		$dom = new \DOMDocument('1.0');
		$svg = $dom->appendChild(new \DOMElement('svg', null, 'http://www.w3.org/2000/svg'));

		array_map(function($file) use (&$dom){
			$tmp = new \DOMDocument('1.0');
			$svg = file_get_contents($file);
			if(is_string($svg) and @file_exists($file)) {
				$svg = str_replace(["\r", "\n", "\t"], [], $svg);
				$svg = preg_replace('/<!--(?!\s*(?:\[if [^\]]+]|<!|>))(?:(?!-->).)*-->/', null, $svg);
				$svg = preg_replace(['/^\<svg/', '/\<\/svg\>/'], ['<symbol', '</symbol>'], $svg);
				$tmp->loadXML($svg);
				$tmp->getElementsByTagName('symbol')->item(0)->setAttribute('id', pathinfo($file, PATHINFO_FILENAME));
				$symbol = $dom->importNode($tmp->getElementsByTagName('symbol')->item(0), true);
				$dom->documentElement->appendChild($symbol);
			}
		}, $svgs);

		$results = $dom->saveXML($dom->getElementsByTagName('svg')->item(0));
		if(is_string($output)) {
			file_put_contents($output, $results);
		}
		else {
			return $results;
		}
	}

	/**
	 * Quick way to use an SVG <symbol>
	 *
	 * @param string  $icon        [ID from the SVG source's symbols]
	 * @param array   $attributes  [key => value set of attributes to set on SVG]
	 * @param string  $src         [The link to the SVG file to use]
	 * @return string              [HTML/SVG element containing a <use>]
	 *
	 * @uses DOMDocument, DOMElement
	 */

	function SVG_use($icon, array $attributes = null, $src = 'images/icons/combined.svg') {
		if(is_string($src) and !is_url($src)) {
			$src = URL . '/' . $src;
		}
		$dom = new \DOMDocument('1.0');
		$svg = $dom->appendChild(new \DOMElement('svg', null, 'http://www.w3.org/2000/svg'));
		$svg->setAttribute('xmlns:xlink', 'http://www.w3.org/1999/xlink');
		$svg->setAttribute('version', '1.1');
		$use = $svg->appendChild(new \DOMElement('use'));
		$use->setAttribute('xlink:href', "{$src}#{$icon}");

		if(is_array($attributes)) {
			foreach($attributes as $attr => $val) {
				$svg->setAttribute($attr, $val);
			}
		}
		return $dom->saveXML($dom->getElementsByTagName('svg')->item(0));
	}

	/**
	 * SVG_us(), but as a data-URI
	 *
	 * @param string  $icon        [ID from the SVG source's symbols]
	 * @param array   $attributes  [key => value set of attributes to set on SVG]
	 * @param string  $src         [The link to the SVG file to use]
	 * @return string              [URL encoded SVG]
	 *
	 * @uses DOMDocument, DOMElement
	 */

	function SVG_use_URI($icon, array $attributes = null, $src = 'images/icons/combined.svg') {
		return 'data:image/svg+xml;utf8,' . rawurlencode(SVG_use($icon, $attributes, $src));
	}

	/**
	 * Trim a sentence to a specified number of words
	 *
	 * @param  string  $text      [original sentence]
	 * @param  integer $max_words [maximum number of words to return]
	 *
	 * @return string             the first $max_words of $text
	 */

	function trim_words($text, $max_words = 0) {
		$words = explode(' ', $text);
		if(count($words) > $max_words) {
			$text = join(' ', array_splice($words, 0, $max_words));
		}
		return $text;
	}
	/**
	 * Download a file by settings headers and exiting with file content
	 *
	 * @param  string $file [local filname]
	 * @param  string $name [name of file when downloaded]
	 *
	 * @return void
	 */

	function download($file = null, $name = null) {
		if(isset($file) and file_exists($file)) {
			if(is_null($name)) {
				$name = filename($file) . '.' . extension($file);
			}
			http_response_code(200);
			header("Pragma: public");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private", false);
			header("Content-type: " . mime_type($file));
			header("Content-Disposition: attachment; filename=\"{$name}\"");
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: " . filesize($file));
			exit(file_get_contents($file));
		}
		else {
			http_response_code(404);
			exit();
		}
	}

	/**
	 * Returns an escaped JSON encoded string,
	 * safe for use as an HTML attribute.
	 *
	 * Useful when writing attibutes into HTML,
	 * but not when doing so in an AJAX response
	 * since JavaScript handles that itself, and
	 * escaping will only cause double-escaped attributes
	 *
	 * @param mixed $data
	 * @return string
	 */

	function json_escape($data) {
		return htmlspecialchars(json_encode($data));
	}

	/**
	 * Remove Leading and trailing single quotes
	 *
	 * @param string $str
	 * @return string
	 */

	function unquote($str = null) {
		return preg_replace("/^\'|\'$/", '', (string)$str);
	}

	/**
	 * Receives a string, returns same string with all words capitalized
	 *
	 * @param string $str
	 * @return string
	 */

	function caps($str = null) {
		return ucwords(strtolower((string)$str));
	}

	/**
	 * Finds the numeric average average of its arguments
	 *
	 * @param mixed args (All values should be numbers, int or float)
	 * @return float (average)
	 * @example average(1, 2) //Returns 1.5
	 * @example average([1.5, 1.6]) //Returns 1.55
	 */

	function average() {
		$args = flatten(func_get_args());
		return array_sum($args) / count($args);
	}

	/**
	 * Is $n even?
	 *
	 * @param int $n
	 * @return boolean
	 */

	function even($n) {
		return ((int)$n % 2) === 0;
	}

	/**
	 * Is $n odd?
	 * Inverse of even()
	 *
	 * @param int $n
	 * @return boolean
	 */

	function odd($n) {
		return !even($n);
	}

	/**
	 * Returns the sum of function's arguments
	 *
	 * @param numeric  [List of numbers]
	 * @return numeric [sum]
	 */

	function sum() {
		return array_sum(func_get_args());
	}

	/**
	 * Returns $n squared
	 *
	 * @param  numeric $n [base number]
	 * @return numeric     [$n squared]
	 */

	function sqr($n = 0) {
		return (is_numeric($n)) ? pow($n, 2) : 0;
	}

	/**
	 * Uses the pythagorean theorem to compute the magnitude of a
	 * hypotenuse in n dimensions
	 *
	 * In any number of dimensions, the hypotenuse is the square root of
	 * the sum of the squares of each dimension.
	 *
	 * @param numeric  [Uses func_get_args, so any number of numeric args]
	 * @return numeric    [magnitude of hypotenuse]
	 */

	function magnitude() {
		return sqrt(array_sum(array_map('sqr', func_get_args())));
	}

	/**
	 * Alias of magnitude
	 *
	 * @param numeric $n  [Uses func_get_args, so any number of numeric args]
	 * @return numeric    [magnitude of hypotenuse]
	 */

	function distance(){
		return call_user_func_array('magnitude', func_get_args());
	}

	/**
	 * Function to remove all tabs and newlines from source
	 * Also strips out HTML comments but leaves conditional statements
	 * such as <!--[if IE 6]>Conditional content<![endif]-->
	 *
	 * @param string $string (Pointer to string to minify)
	 * @return string
	 * @example minify("<!--Test-->\n<!--[if IE]>...<[endif]-->\n<p>...</p>") /Leaves only "<p>...</p>"
	 */

	function minify(&$string = null) {
		$string = str_replace(["\r", "\n", "\t"], [], trim((string)$string));
		$string = preg_replace('/<!--(?!\s*(?:\[if [^\]]+]|<!|>))(?:(?!-->).)*-->/', null, $string);
		return $string;
	}

	/**
	 * Converts date/time from one format to another
	 *
	 * @link http://php.net/manual/en/function.strtotime.php
	 * @param mixed $from (Original time)
	 * @param string $format
	 * @param string $offset
	 * @return string
	 * @example convert_date('Now', 'r', '+2 weeks')
	 */

	function convert_date($from = null, $format = 'U', $offset = 'Now') {
		if(is_string($from)) $from = strtotime($from);
		elseif(isset($from) and !is_int($from)) $from = time();
		if($format === 'U') {
			return (int)date($format, strtotime($offset, $from));
		}
		else {
			return date($format, strtotime($offset, $from));
		}
	}

	/**
	 * Computes the length in seconds of $length
	 *
	 * This can simply be computed by using strtotime
	 * against the Unix Epoch (t = 0)
	 *
	 * @param string $time
	 * @return int
	 * @example get_time_offset('1 week'); //returns 604800
	 * @example get_time_offset('1 week +1 second'); //returns 604801
	 */

	function get_time_offset($time) {
		return strtotime('+' . $time, 0);
	}

	/**
	 * Returns http content from request.
	 *
	 * @link http://www.php.net/manual/en/book.curl.php
	 * @param string $request[, string $method]
	 * @return string
	 * @todo Handle both GET and POST methods
	 */

	function curl($request = null, $method = 'get') {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, (string)$request);
		curl_setopt($ch, CURLOPT_FRESH_CONNECT, TRUE);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_TIMEOUT,30);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true );
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}

	/**
	 * See previous curl()
	 *
	 * @param string $url,
	 * @param mixed $request
	 * @return string
	 */

	function curl_post($url = null, $request = null) {
		$requestBody = http_build_query($request);
		$connection = curl_init();
		curl_setopt($connection, CURLOPT_URL, (string)$url);
		curl_setopt($connection, CURLOPT_TIMEOUT, 30 );
		curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($connection, CURLOPT_POST, count($request));
		curl_setopt($connection, CURLOPT_POSTFIELDS, $requestBody);
		curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($connection, CURLOPT_FAILONERROR, 0);
		curl_setopt($connection, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($connection, CURLOPT_HTTP_VERSION, 1);		// HTTP version must be 1.0
		$response = curl_exec($connection);
		return $response;
	}

	/**
	 * Converts ['path', 'to', 'something'] to '/path/to/something/'
	 *
	 * @param  array  $path_array [Path components]
	 * @return string             [Final path]
	 */

	function array_to_path(array &$path_array) {
		return DIRECTORY_SEPARATOR . trim(join(DIRECTORY_SEPARATOR, $path_array), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
	}

	/**
	 * Build a path using a set of arguments
	 *
	 * @param string  [Any number of directories using func_get_args()]
	 * @return string [Final path]
	 */

	function build_path() {
		return array_to_path(func_get_args());
	}

	/**
	 * Get required Apache & PHP modules from settings.ini,
	 * compare against loaded modules, and return the difference
	 *
	 * @param void
	 * @return mixed (null if all loaded, otherwise object of two arrays)
	 * @example
	 * $missing = module_test()
	 * if(is_null($missing))...
	 * else ...
	 */

	function module_test() {
		$settings = \core\ini::load('settings');

		/**
		 * First, check if the directives are set in settings.ini
		 * If not, return null
		 */

		if(
			!isset($settings->php_modules)
			or !isset($settings->apache_modules)
		) {
			return null;
		}

		$missing = new \stdClass();

		/**
		 * Missing PHP modules are the difference between an
		 * arrray of required modules and the array of loaded modules
		 */

		$missing->php = array_diff(
			explode(',', str_replace(' ', null, $settings->php_modules)),		//Convert the list in settings.ini to an array
			get_loaded_extensions()												//Get array of loaded PHP modules
		);
		$missing->apache = array_diff(
			explode(',', str_replace(' ', null, $settings->apache_modules)),	//Convert the list in settings.ini to an array
			apache_get_modules()												//Get array of loaded Apache modules
		);

		if(count($missing->php) or count($missing->apache)) {					//If either is not empty, return $missing
			return $missing;
		}
		else {																	//Otherwise return null
			return null;
		}
	}

	/**
	 * [Returns days of a week in an array (Mon - Fri | Sun - Sat)]
	 *
	 * @param  bool $full           [Mon-Fri only]
	 *
	 * @return array                [Array of requested days]
	 */

	function weekdays($full = true) {
		if($full) {
			return [
				'Sunday',
				'Monday',
				'Tuesday',
				'Wednesday',
				'Thursday',
				'Friday',
				'Saturday'
			];
		}
		return [
			'Monday',
			'Tuesday',
			'Wednesday',
			'Thursday',
			'Friday'
		];
	}
?>
