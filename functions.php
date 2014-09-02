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

	spl_autoload_extensions('.php');
	spl_autoload_register();				 //Load class by naming it

	init();

	function init($session = true) {
		/**
		 * Initial configuration. Setup include_path, gather database
		 * connection information, set undefined properties to
		 * default values, start a new session, and set nonce
		 *
		 * @param bool $session
		 * @return array $info
		 */

		//Include current directory, config/, and classes/ directories in include path
		set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . PATH_SEPARATOR . __DIR__  . DIRECTORY_SEPARATOR . 'config' . PATH_SEPARATOR . __DIR__ . DIRECTORY_SEPARATOR . 'classes');

		if(file_exists('./config/define.ini')) {
			foreach(parse_ini_file('./config/define.ini') as $key => $value) {
				define(strtoupper(preg_replace('/\s|-/', '_', $key)), $value);
			}
		}

		if(!defined('BASE')) define('BASE', __DIR__);
		if(!defined('URL')) ($_SERVER['DOCUMENT_ROOT'] === __DIR__ . DIRECTORY_SEPARATOR or $_SERVER['DOCUMENT_ROOT'] === __DIR__) ? define('URL', "${_SERVER['REQUEST_SCHEME']}://{$_SERVER['SERVER_NAME']}") : define('URL', "${_SERVER['REQUEST_SCHEME']}://{$_SERVER['SERVER_NAME']}/" . end(explode('/', BASE)));
		if($session) {
			session::load();
			nonce(50);									// Set a nonce of n random characters
		}
	}

	function config($settings_file = 'settings') {
		/**
		* Load and configure site settings
		* Loads all files in requires directive
		* Setup custom error handler
		*
		* @parmam void
		* @return void
		*/


		$settings = ini::load((string)$settings_file);
		if(isset($settings->path)) {
			set_include_path(get_include_path() . PATH_SEPARATOR . preg_replace('/(\w)?,(\w)?/', PATH_SEPARATOR, $settings->path));
		}

		if(isset($settings->requires)) {
			foreach(explode(',', $settings->requires) as $file) {
				require_once(__DIR__ . '/' . trim($file));
			}
		}

		if(isset($settings->time_zone)) {
			date_default_timezone_set($settings->time_zone);
		}

		if(isset($settings->autoloader)) {
			spl_autoload_register($settings->autoloader);
		}

		$error_handler = (isset($settings->error_handler)) ? $settings->error_handler : 'error_reporter_class';

		//Error Reporting Levels: http://us3.php.net/manual/en/errorfunc.constants.php
		if(isset($settings->debug)) {
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
			error_reporting(E_COMPILE_ERROR|E_RECOVERABLE_ERROR|E_ERROR|E_CORE_ERROR);
		}
	}

	function error_reporter_class($error_level = null, $error_message = null, $file = null, $line = null, $scope = null) {
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

		static $reporter = null;

		if(is_null($reporter)) {
			$settings = ini::load('settings');
			$reporter = error_reporter::load((isset($settings->error_method)) ? $settings->error_method : 'log');
			if(is_null($settings->error_method or $settings->error_method === 'log')) {
				$reporter->log = (isset($settings->error_log)) ? $settings->error_log : 'errors.log';
			}
		}

		return $reporter->report((string)$error_level, (string)$error_message, (string)$file, (string)$line, $scope);
	}

	function load() {									// Load resource from components directory
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
		 * @usage load(string | array[string | array[, ...]]*)
		 */

		static $DB, $load, $settings, $session, $login, $cookie;
		$found = true;

		if(is_null($load)) {
			$DB = _pdo::load();
			$settings = ini::load('settings');
			$session = session::load();
			$login = login::load();
			$cookie = cookies::load();
			$load = (defined('THEME')) ? function($fname, &$found) use ($DB, $settings, $session, $cookie, $login) {
				(include(BASE . "/components/" . THEME . DIRECTORY_SEPARATOR . $fname .".php")) or $found = false;
			} : function($fname, &$found) use ($DB, $settings, $session, $cookie, $login) {
				(include(BASE . "/components/{$fname}.php")) or $found = false;
			};
		}

		foreach(flatten(func_get_args()) as $fname) {	// Unknown how many arguments passed. Loop through function arguments array
			$load((string)$fname, $found);
		}
		return $found;
	}

	function load_results() {
		/**
		 * Similar to load(), except that it returns rather than prints
		 *
		 * @usage(string | array[string | array[, ...]]*)
		 * @param mixed (string, arrays, ... whatever. They'll be converted to an array)
		 * @return string (results echoed from load())
		 */

		ob_start();
		load(func_get_args());
		return ob_get_clean();
	}

	function parse_json_file($filename = null, $assoc = false, $depth = 512, $options = 0) {
		/**
		 * Reads a file and returns a json_decoded object
		 *
		 * @link http://php.net/manual/en/function.json-decode.php
		 * @param string $filename
		 * @param bool $assoc
		 * @param int $depth
		 * @param int $options
		 * @return stdClass Object
		 */

		return json_decode(file_get_contents("{$filename}.json", true), $assoc, $depth, $options);
	}

	function strip_enclosing_tag($html = null) {
		/**
		 * strips leading trailing and closing tags, including leading
		 * new lines, tabs, and any attributes in the tag itself.
		 *
		 * @param $html (html content to be stripping tags from)
		 * @return string (html content with leading and trailing tags removed)
		 * @usage strip_enclosing_tags('<div id="some_div" ...><p>Some Content</p></div>')
		 */

		return preg_replace('/^\n*\t*\<.+\>|\<\/.+\>$/', '', (string)$html);
	}

	function html_join($tag, array $content = null, array $attributes = null) {
		/**
		 * Converts an array into a string of HTML tags containing
		 * the values of the array... useful for tables and lists.
		 *
		 * @param string $tag (Surrounding HTML tag)
		 * @param array $content
		 * @param array $attributes
		 * @return string
		 */

		$tag = preg_replace('/[^a-z]/', null, strtolower((string)$tag));
		$attributes = array_to_attributes($attributes);
		return "<{$tag} {$attributes}>" . join("</{$tag}><{$tag}>", $content) . "</{$tag}>";
	}

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
		foreach($attributes as $name => $value) $str .= " {$name}=\"{$value}\"";
		return trim($str);
	}

	function debug($data = null, $comment = false) {
		/**
		 * Prints out information about $data
		 * Wrapped in html comments or <pre><code>
		 *
		 * @param mixed $data[, boolean $comment]
		 * @return void
		 */

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

	function require_login($role = null, $exit = 'notify') {
		$login = login::load();

		if(!$login->logged_in) {
			switch((string)$exit) {
				case 'notify': {
					$resp = new json_response();
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
			$resp = new json_response();
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

	function check_nonce() {
		/**
		 * A nonce is a random string used for validation.
		 * One is generated for every session, and is used to
		 * prevent such things as brute force attacks on form submission.
		 * Without checking a nonce, it becomes easier to brute force login attempts
		 *
		 * @param void
		 * @return void
		 */

		if(!(array_key_exists('nonce', $_POST) and array_key_exists('nonce', $_SESSION)) or $_POST['nonce'] !== $_SESSION['nonce']) {
			$resp = new json_response();
			$resp->notify(
				'Something went wrong :(',
				'Your session has exired. Try refreshing the page',
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

	function CSP() {
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
		 */

		$CSP = '';									 // Begin with an empty string
		$CSP_Policy = parse_ini_file('csp.ini');	// Read ini
		if(!$CSP_Policy) return;
		$enforce = array_remove('enforce', $CSP_Policy);
		if(is_null($enforce)) $enforce = true;
		foreach($CSP_Policy as $type => $src) {		// Convert config array to string for CSP header
			$CSP .= "{$type} {$src};";
		}
		$CSP = str_replace('%NONCE%', $_SESSION['nonce'], $CSP);
		if($enforce) {								// If in debug mode, CSP should be "report-only"
													// Set headers for all prefixed versions
													//[TODO] Use UA sniffing to only set correct header
			header("Content-Security-Policy: $CSP");
			//header("X-Content-Security-Policy: $CSP");
			//header("X-Webkit-CSP: $CSP");
		}
		else{										// If not, CSP will be enforced
			header("Content-Security-Policy-Report-Only: $CSP");
		}
	}

	function localhost() {
		/**
		 * Checks to see if the server is also the client.
		 *
		 * @param void
		 * @return boolean
		 */

		return ($_SERVER['REMOTE_ADDR'] === $_SERVER['SERVER_ADDR']);
	}

	function https() {
		/**
		 * Returns whether or not this is a secure (HTTPS) connection
		 *
		 * @param void
		 * @return boolean
		 */

		return (isset($_SERVER['HTTPS']) and $_SERVER['HTTPS']);
	}

	function DNT() {
		/**
		 * Checks and returns whether or not Do-Not-Track header
		 * requests that we not track the client
		 *
		 * @param void
		 * @return boolean
		 */

		return (isset($_SERVER['HTTP_DNT']) and $_SERVER['HTTP_DNT']);
	}

	function is_ajax() {							// Try to determine if this is and ajax request
		/**
		 * Checks for the custom Request-Type header sent in my ajax requests
		 *
		 * @param void
		 * @return boolean
		 */

		return (isset($_SERVER['HTTP_REQUEST_TYPE']) and $_SERVER['HTTP_REQUEST_TYPE'] === 'AJAX');
	}

	function header_type($type = null) {							// Set content-type header.
		/**
		 * Sets HTTP Content-Type header
		 * @param string $type
		 * @return void
		 */

		header('Content-Type: ' . (string)$type . PHP_EOL);
	}

	function define_UA() {								// Define Browser and OS according to user-agent string
		/**
		 * Defines a variety of things using the HTTP_USER_AGENT header,
		 * such as operating system and browser
		 *
		 * @param void
		 * @return void
		 */

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

	function nonce($length = 50) {						// generate a nonce of $length random characters
		/**
		 * Generates a random string to be used for form validation
		 *
		 * @link http://www.html5rocks.com/en/tutorials/security/content-security-policy/
		 * @param integer $length
		 * @return string
		 */

		$length = (int)$length;
		if(array_key_exists('nonce', $_SESSION)) {	// Use existing nonce instead of a new one
			return $_SESSION['nonce'];
		}
		//We are going to shuffle an alpha-numeric string to get random characters
		$str = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
		if(strlen($str) < $length) {					// $str length is limited to length of available characters. Be recursive for extra length
			$str .= nonce($length - strlen($str));
		}
		$_SESSION['nonce'] = $str;							// Save this to session for re-use
		return $str;
	}

	function same_origin() {							// Determine if request is from us
		/**
		 * Checks whether or not the current request was sent
		 * from the same domain
		 *
		 * @param void
		 * @return boolean
		 */

		if(isset($_SERVER['HTTP_ORIGIN'])) {
			$origin = $_SERVER['HTTP_ORIGIN'];
		}
		elseif(isset($_SERVER['HTTP_REFERER'])) {
			$origin = $_SERVER['HTTP_REFERER'];
		}

		$name = '/^http(s)?' .preg_quote('://' . $_SERVER['SERVER_NAME'], '/') . '/';
		return (isset($origin) and preg_match($name, $origin));
	}

	function sub_root() {
		/**
		 * @param void
		 * @return string (Directory one level below DOCUMENT_ROOT)
		 */

		$root = trim($_SERVER['DOCUMENT_ROOT'], '/');
		$sub = preg_replace('/' . preg_quote(end(explode('/', $root))) . '/', '', $root);
		return $sub;
	}

	function array_remove($key = null, array &$array) {
		/**
		 * Remove from array by key and return it's value
		 *
		 * @param string $key, array $array
		 * @return array | null
		 */

		$key = (string)$key;
		if(array_key_exists($key, $array)) {
			$val = $array[$key];					// Need to store to variable before unsetting, then return the variable
			unset($array[$key]);
			return $val;
		}
		else return null;
	}

	function array_keys_exist() {
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
		 * @example array_keys_exist('red', 'green', 'blue', ['red' => '#f00', 'green' => '#0f0', 'blue' => '#00f']) // true
		 */

		$keys = func_get_args();
		$arr = array_pop($keys);
		$arr = array_keys($arr);

		foreach($keys as $key) {
			if(!in_array($key, $arr, true)) return false;
		}
		return true;
	}

	function flatten() {
		/**
		 * Convert a multi-dimensional array into a simple array
		 *
		 * Can't say that I'm entirely sure how it does what it does,
		 * only that it works
		 *
		 * @param mixed args
		 * @return array
		 */

		return iterator_to_array(new RecursiveIteratorIterator(
			new RecursiveArrayIterator(func_get_args())),FALSE);
	}

	function list_array(array $array) {
		/**
		 * Prints out an unordered list from an array
		 * @param array $array
		 * @return void
		 */

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

	function is_assoc(array $array) {
		/**
		 * Checks if an array is associative array
		 * (A single index is a string)
		 *
		 * @param array $array
		 * @return bool
		 */

		return (bool)count(array_filter(array_keys($array), 'is_string'));
	}

	function is_indexed(array $array) {
		/**
		 * Checks if an array is indexed(numerical)
		 *
		 * @param array $array
		 * @return bool
		 */

		return (bool)count(array_filter(array_keys($array), 'is_int'));
	}

	function is_a_number($n = null) {
		/**
		 * Because I was tired of writing this... the ultimate point of programming, after all
		 *
		 * @param mixed $n
		 * @return boolean
		 */

		return preg_match('/^\d+$/', $n);
	}

	function is_not_a_number($n = null) {
		/**
		 * Opposite of previous.
		 *
		 * @param mixed $n
		 * @return boolean
		 */

		return !is_a_number($n);
	}

	function is_email($str = null) {
		/**
		 * Checks if $str validates as an email
		 *
		 * @param string $str
		 * @return bolean
		 * @link http://php.net/manual/en/filter.filters.validate.php
		 */

		return filter_var($str, FILTER_VALIDATE_EMAIL);
	}

	function is_url($str = null) {
		/**
		 * Checks if $str validates as a URL
		 *
		 * @param string $str
		 * @return bolean
		 * @link http://php.net/manual/en/filter.filters.validate.php
		 */

		return filter_var($str, FILTER_VALIDATE_URL);
	}

	function check_inputs(array $inputs, array $source = null) {
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
		 * @usage find_invalid_inputs(['num' => '\d', 'user' => is_email($source['user])], $source)
		 */

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

	function pattern($type = null) {
		/**
		 * Useful for pattern attributes as well as server-side input validation
		 * Must add regexp breakpoints for server-side use ['/^$pattern$/']
		*
		 * @param string $type
		 * @return string (regexp)
		 */

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
				$pattern = '(19|20)\d{2}-(0?[1-9]|1[12])-(0?[1-9]|[12]\d?|3[01]) T([01]\d|2[0-3])(:[0-5]\d)+';
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

	function utf($string = null) {
		/**
		 * Converts characters to UTF-8. Replaces special chars.
		 *
		 * @param string $string
		 * @return (string UTF-8 converted)
		 * @example utf('This & that') //Returns 'This &amp; that'
		 */

		return htmlentities((string)$string, ENT_QUOTES | ENT_HTML5,"UTF-8");
	}

	function ls($path = null, $ext = null, $strip_ext = null) {
		/**
		 * List files in given path. Optional extension and strip extension from results
		 *
		 * @param [string $path[, string $ext[, boolean $strip_ext]]]
		 * @return array
		 */

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

	function encode($file = null) {
		/**
		 * Base 64 encode $file. Does not set data: URI
		 * @param string $file
		 * @return string (base_64 encoded)
		 */

		$file = (string)$file;
		if(file_exists($file)) return base64_encode(file_get_contents($file));
	}

	function mime_type($file = null) {
		/**
		 * Determine the mime-type of a file
		 * using file info or file extension
		 *
		 * @param string $file
		 * @return string (mime-type)
		 * @example mime_type(path/to/file.txt) //Returns text/plain
		 */

		//Make an absolute path if given a relative path in $file

		$file = (string)$file;
		if(substr($file, 0, 1) !== '/') $file = BASE . "/$file";

		$unsupported_types = [
			'css' => 'text/css',
			'js' => 'application/javascript',
			'svg' => 'image/svg+xml',
			'woff' => 'application/font-woff',
			'appcache' => 'text/cache-manifest',
			'm4a' => 'audio/mp4',
			'ogg' => 'audio/ogg',
			'oga' => 'audio/ogg',
			'ogv' => 'vidoe/ogg'
		];

		if(array_key_exists(extension($file), $unsupported_types)) {
			$mime = $unsupported_types[extension($file)];
		}
		else {
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$mime = finfo_file($finfo, $file);
			finfo_close($finfo);
		}
		return $mime;
	}

	function data_uri($file = null) {
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

		$file = (string)$file;
		return 'data:' . mime_type($file) . ';base64,' . encode($file);
	}

	function extension($file = null) {
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

		return '.' . pathinfo((string)$file, PATHINFO_EXTENSION);
	}

	function filename($file = null) {
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

		return pathinfo((string)$file, PATHINFO_FILENAME);
	}

	function unquote($str = null) {
		/**
		 * Remove Leading and trailing single quotes
		 *
		 * @param string $str
		 * @return string
		 */

		return preg_replace("/^\'|\'$/", '', (string)$str);
	}

	function caps($str = null) {
		/**
		 * Receives a string, returns same string with all words capitalized
		 *
		 * @param string $str
		 * @return string
		 */

		return ucwords(strtolower((string)$str));
	}

	function average() {
		/**
		 * Finds the numeric average average of its arguments
		 *
		 * @param mixed args (All values should be numbers, int or float)
		 * @return float (average)
		 * @example average(1, 2) //Returns 1.5
		 * @example average([1.5, 1.6]) //Returns 1.55
		 */

		$args = flatten(func_get_args());
		return array_sum($args) / count($args);
	}

	function minify(&$string = null) {
		/**
		 * Function to remove all tabs and newlines from source
		 * Also strips out HTML comments but leaves conditional statements
		 * such as <!--[if IE 6]>Conditional content<![endif]-->
		 *
		 * @param string $string (Pointer to string to minify)
		 * @return string
		 * @example minify("<!--Test-->\n<!--[if IE]>...<[endif]-->\n<p>...</p>") /Leaves only "<p>...</p>"
		 */

		$string = str_replace(["\r", "\n", "\t"], [], trim((string)$string));
		$string = preg_replace('/<!--(?!\s*(?:\[if [^\]]+]|<!|>))(?:(?!-->).)*-->/', null, $string);
		return $string;
	}

	function curl($request = null, $method = 'get') {
		/**
		 * Returns http content from request.
		 *
		 * @link http://www.php.net/manual/en/book.curl.php
		 * @param string $request[, string $method]
		 * @return string
		 */

		//[TODO] Handle both GET and POST methods
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

	function curl_post($url = null, $request = null) {			//cURL for post instead of get
		/**
		 * See previous curl()
		 *
		 * @param string $url,
		 * @param mixed $request
		 * @return string
		 */

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

	function module_test() {
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

		$settings = ini::load('settings');


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

		$missing = new stdClass();

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
?>
