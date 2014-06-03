<?php
	/**
	 * @author Chris Zuber <shgysk8zer0@gmail.com>
	 * @copyright 2014, Chris Zuber
	 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3 (GPL-3.0)
	 * @package core_shared
	 * @version 2014-04-19
	 */

	if (!defined('PHP_VERSION_ID')) {
		$version = explode('.', PHP_VERSION);
		define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
	}
	if (PHP_VERSION_ID < 50207) {
		define('PHP_MAJOR_VERSION',   $version[0]);
		define('PHP_MINOR_VERSION',   $version[1]);
		define('PHP_RELEASE_VERSION', $version[2]);
	}

	spl_autoload_register('load_class');				 //Load class by naming it
	init();

	function regexp($str) {								//Make regular expression from string
		/**
		 * @param string $str
		 * @return string in regular expression format /string\.\.\./
		 */
		return '/' . preg_quote($str, '/') . '/';
	}

	function strip_enclosing_tag($html) {
		/**
		 * strips leading trailing and closing tags, including leading
		 * new lines, tabs, and any attributes in the tag itself.
		 *
		 * @param $html (html content to be stripping tags from)
		 * @return string (html content with leading and trailing tags removed)
		 * @usage strip_enclosing_tags('<div id="some_div" ...><p>Some Content</p></div>')
		 */

		return preg_replace('/^\n*\t*\<.+\>|\<\/.+\>$/', '', $html);
	}

	function json_response($resp) {
		/**
		 * Exits, printing out the $resp array as a JSON encoded string.
		 *
		 * Intended to be handled by 'handleJSON', there are a usually
		 * only a handful of array key/values, in different depths of the array.
		 *
		 * Although any array or object could be passed as $resp, these
		 * are the only values handled in handleJSON
		 *
		 * @depreciated
		 * @usage
		 * $resp = [
		 * 	'html' => [
		 * 		'CSS-Selector' => 'HTML content' | load_results($files?),
		 * 		'CSS-Selector2' => '...',
		 * 		...
		 * 	],
		 * 	'append | prepend | before | after' => [
		 * 		'CSS-Selector' => 'HTML content' | load_results($files?),
		 * 		...
		 * 	],
		 * 	'attributes' => [
		 * 		'CSS-Selector' => [
		 * 			'attribute' => 'value' | true | false,
		 * 			'attribute2' => ...
		 * 			...
		 * 		],
		 * 		...
		 * 	]
		 * 	'notify' => [
		 * 		'tile' => 'Notification Title',
		 * 		'body' => 'Notifcation Message',
		 * 		'icon' => 'path/file.ext',
		 * 		'on[event]' => function(){}		//Not sure if this will work, depending on Content-Security-Policy for script handling of eval
		 * 	]
		 * ]
		 * @param array $response
		 * @return null
		 */

		header('Content-Type: application/json');
		exit(json_encode($resp));
	}

	function debug($data, $comment = false) {
		/**
		 * Prints out information about $data
		 * Wrapped in html comments or <pre><code>
		 *
		 * @param mixed $data[, boolean $comment]
		 * @return void
		 */

		if($comment) {
			echo '<!--';
			print_r($data);
			echo '-->';
		}
		else {
			echo '<pre><code>';
			print_r($data);
			echo '</code></pre>';
		}
	}

	function require_login($role = null, $exit = 'notify') {
		$login = login::load();

		if(!$login->logged_in) {
			switch($exit) {
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
					http_status(403);
					exit();
				}

				case 'return' : {
					return false;
				}

				default: {
					http_status(403);
					exit();
				}
			}
		}

		elseif(isset($role) and strlen($role)) {
			$role = strtolower($role);
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
					//'You do not have permission to do that',
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

	function init($site = null) {						// Get info from .ini file
		/**
		 * @param string $site
		 * @return array $info
		 */
		ini_set('include_path', ini_get('include_path') . ':' . __DIR__ . ":" . __DIR__ . "/classes");

		$info = parse_ini_file("connect.ini");
		$connect = ini::load('connect');
		if(!isset($connect->site)) {
			if(is_null($site)) {
				($_SERVER['DOCUMENT_ROOT'] === __DIR__ . '/' or $_SERVER['DOCUMENT_ROOT'] === __DIR__) ? $connect->site = end(explode('/', preg_replace('/\/$/', '', $_SERVER['DOCUMENT_ROOT']))) : $connect->site = explode('/', $_SERVER['PHP_SELF'])[1];
			}
		}
		if(!isset($connect->user)) $conenct->user = $connect->site;
		if(!isset($connect->database)) $connect->database = $connect->user;
		if(!isset($connect->server)) $connect->server = 'localhost';
		if(!isset($connect->debug)) $connect->debug = true;
		if(!isset($connect->type)) $connect->type = 'mysql';
		if($connect->server !== 'localhost' and is_null($connect->port)) $connect->port = '3306';
	}

	function config() {								// Initial Setup
		/**
		* Sets timezone, starts session named according to site
		* Defines BASE and URL and sets Content-Security-Policy headers
		*
		* @parmam void
		* @return void
		*/

		$connect = ini::load('connect');
		date_default_timezone_set('America/Los_Angeles');
		//Error Reporting Levels: http://us3.php.net/manual/en/errorfunc.constants.php
		($connect->debug) ? error_reporting(E_COMPILE_ERROR|E_RECOVERABLE_ERROR|E_ERROR|E_CORE_ERROR) : error_reporting(E_CORE_ERROR);
		if(!defined('BASE')) define('BASE', __DIR__);
		if(!defined('URL')) ($_SERVER['DOCUMENT_ROOT'] === __DIR__ . '/' or $_SERVER['DOCUMENT_ROOT'] === __DIR__) ? define('URL', "${_SERVER['REQUEST_SCHEME']}://{$_SERVER['SERVER_NAME']}") : define('URL', "${_SERVER['REQUEST_SCHEME']}://{$_SERVER['SERVER_NAME']}/{$connect->site}");
		new session($connect->site);
		nonce(50);									// Set a nonce of n random characters
	}

	function CSP() {								//Sets Content-Security-Policy from csp.ini
		/**
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
		$reg = new regexp($CSP);					// Prepare to use regexp to set CSP nonce with the one in $_SESSION
		$CSP = $reg->replace('%NONCE%')->with("{$_SESSION['nonce']}")->execute();
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

	function localhost() {							// Just checks of client is also server
		/**
		 * @param void
		 * @return boolean
		 */
		return ($_SERVER['REMOTE_ADDR'] === $_SERVER['SERVER_ADDR']);
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

	function array_remove($key, &$array) {			// Remove from array by key and return it's value
		/**
		 * @param string $key, array $array
		 * @return array | null
		 */
		if(array_key_exists($key, $array)) {
			$val = $array[$key];					// Need to store to variable before unsetting, then return the variable
			unset($array[$key]);
			return $val;
		}
		else return null;
	}

	function flatten() {							// Convert a multi-dimensional array into a simple array
		/**
		 * @param mixed args
		 * @return array
		 */
		return iterator_to_array(new RecursiveIteratorIterator(
			new RecursiveArrayIterator(func_get_args())),FALSE);
	}

	function is_a_number($n) {						// Because I was tired of writing this... the ultimate point of programming, after all
		/**
		 * @params mixed $n
		 * @return boolean
		 */
		return preg_match('/^\d+$/', $n);
	}

	function is_not_a_number($n) {					// Opposite of previous.
		/**
		 * @params mixed $n
		 * @return boolean
		 */
		return !is_a_number($n);
	}

	function https() {								// Just returns a boolean value for if schema is https
		/**
		 * @params void
		 * @return boolean
		 */
		return (isset($_SERVER['HTTPS']) and $_SERVER['HTTPS']);
	}

	function DNT() {								// Is DNT set and on?
		/**
		 * @params void
		 * @return boolean
		 */
		return (isset($_SERVER['HTTP_DNT']) and $_SERVER['HTTP_DNT']);
	}

	function ls($path = __DIR__, $ext = null, $strip_ext = null) {			// List files in given path. Optional extension and strip extension from results
		/**
		 * @param [string $path[, string $ext[, boolean $strip_ext]]]
		 * @return array
		 */
		$files = array_diff(scandir($path), array('.', '..'));				// Get array of files. Remove current and previous directory (. & ..)
		$results = array();
		if(isset($ext)) {													//$ext has been passed, so let's work with it
			//Convert $ext into regexp
			$ext = '/' . preg_quote('.' . $ext, '/') .'/';					// Convert for use in regular expression
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

	function load_results() {
		/**
		 * @usage(string | array[string | array[, ...]]*)
		 * @param mixed (string, arrays, ... whatever. They'll be converted to an array)
		 * @return string (results echoed from load())
		 */
		ob_start();
		load(func_get_args());
		return ob_get_clean();
	}

	function load() {									// Load resource from components directory
		/**
		 * @usage(string | array[string | array[, ...]]*)
		 * @params mixed args
		 * @return void
		 */
		$found = true;
		$DB = _pdo::load();								// Include $DB here, so it is in the currecnt scope. Saves multiple uses of global
		foreach(flatten(func_get_args()) as $fname) {	// Unknown how many arguments passed. Loop through fucntion arguments array
			(include(BASE . "/components/{$fname}.php")) or $found = false;
		}
		unset($DB);
		return $found;
	}

	function load_file() {
		$resp = '';
		$files = flatten(func_get_args());
		foreach($files as $file) $resp .= file_get_content(BASE . "components/{$file}");
	}

	function load_class($class) {						// Load class from Classes directory
														//PHP uses include_path, so use that. I've added the classes directory to include_path already
		/**
		 * @params string $class
		 * @return void
		 */
		require_once "{$class}.php";
	}

	function same_origin() {							// Determine if request is from us
		/**
		 * @params void
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

	function sub_root() {								// Just get directory up on level from $_SERVER['DOCUMENT_ROOT']
		/**
		 * @params void
		 * @return string
		 */
		$root = preg_replace('/\/$/', '', $_SERVER['DOCUMENT_ROOT']);	// Strip off the '/' at the end of DOCUMENT_ROOT
		$sub = preg_replace('/' . preg_quote(end(explode('/', $root))) . '/', '', $root);
		return $sub;
	}

	function extension($file) {
		return end(explode('.', $file));
	}

	function unquote($str) {							// Remove Leading and trailing single quotes
		/**
		 * @params string $str
		 * @return string
		 */
		return preg_replace("/^\'|\'$/", '', $str);
	}

	function array_keys_exist() {						// Check if all keys exist in array
		/**
		* Use array_key_exists on each key.
		* Return false as soon as one is missing
		* $args is all arguments given to function
		* Since final argument is array, seperate that and remove from length
		*
		 * @params string[, string, .... string] array
		 * @return boolean
		 */
		$args = func_get_args();
		$arr = end($args);
		$length = func_num_args() - 1;
		for($i = 0; $i < $length; $i++) {
			if(!array_key_exists($args[$i], $arr)) return false;
		}
		return true;
	}

	function caps($str) {								// Receives a string, returns same string with all words capitalized
		/**
		 * @params string $str
		 * @return string
		 */
		return ucwords(strtolower($str));
	}

	function list_array($array) {						// Lists array as <ul>
		/**
		 * @params array
		 * @return void
		 */
		echo "<ul>";
		foreach($array as $key => $entry) echo "<li>{$key}: {$entry}</li>";
		echo "</ul>";
	}

	function array_to_table($table) {					// Converts an associative array to a table
		/**
		 * @params array $tabel
		 * @return void
		 */
		//[TODO] Use array keys instead of looping through and grabbing keys
		$headers = table_headers($table);
		echo "<table border=\"1\">";
			echo "<thead>";
			foreach($headers as $header) echo "<th>{$header}</th>";
			echo "</thead>\n<tbody>";
			foreach($table as $row) {
				echo "<tr>";
				foreach($row as $cell) {
					echo "<td>{$cell}</td>";
				}
				echo "</tr>";
			}
		echo "</tbody>\n</table>";
	}

	function table_headers($table) {					//Unneeded, but keeping for now.
		/**
		 * @params array $table
		 * @return array
		 */
		//[TODO] Stop using this and use array keys instead
		$headers = array();
		$row = $table[0];
		foreach($row as $key => $value) {
			array_push($headers, $key);
		}
		return $headers;
	}

	function define_UA() {								// Define Browser and OS according to user-agent string
		/**
		 * @params void
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
				 define('BOWSER', 'Unknown');
				 define('OS', 'Unknown');
			};
		}
	}

	function nonce($length = 50) {						// generate a nonce of $length random characters
		/**
		 * @params integer $length
		 * @return string
		 */

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

	function encode($file) {							// Base 64 encode $file. Does not set data: URI
		/**
		 * @params string $file
		 * @return string (base_64 encoded)
		 */
		if(file_exists($file)) return base64_encode(file_get_contents($file));
	}

	function mime_type($file) {
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

	function data_uri($file) {
		return 'data:' . mime_type($file) . ';base64,' . encode($file);
	}

	function clean($string, $rep=null) {				//Strips dangerous characters from string.
		/**
		 * @depreciated
		 * @params string $string[, string $rep]
		 * @return string
		 */
		if($rep === null) $rep = array("<",">","/",";","\\","&","'","\"","{","}","[","]","(",")");
		else $rep = explode(" ",$rep);
		return str_replace($rep,"",$string);
	}

	function curl($request, $method = 'get') {			// Returns http content from request.
		/**
		 * @params string $request[, string $method]
		 * @return string
		 */
		//[TODO] Handle both GET and POST methods
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $request);
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

	function curl_post($url, $request) {			//cURL for post instead of get
		/**
		 * @params string $url, string $request
		 * @return string
		 */

		$requestBody = http_build_query($request);
		$connection = curl_init();
		curl_setopt($connection, CURLOPT_URL, $url);
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

	function minify($src) {							//Trims extra spaces and removes tabs and new lines.
		/**
		 * @params string $src
		 * @return string
		 */
		return preg_replace(array('/\t/', '/\n/', '/\r\n/'), array(), trim($src));
	}

	function strip_path() {							//For use with server redirects. Gets just redirect path as array
		/**
		 * @depreciated
		 * @params void
		 * @return array
		 */
		$path = explode('/',$_SERVER['REQUEST_URI']);
		$_PASSED = array();
		for($n = 1;$n < count($path); $n++) $_PASSED[$path[$n]] = $path[++$n];
		return $_PASSED;
	}

	function pattern($type = null) {					 //Returns regular expression based on $type

		/**
		 * Useful for pattern attributes as well as server-side input validation
		 * Must add regexp breakpoints for server-side use ['/^$pattern$/']
		*
		 * @params string $type
		 * @return string (regexp)
		 */
		switch($type) {
			case "text":
				$pattern = "(\w+(\ )?)+";
				break;
			case "name":
				$pattern = "[A-Za-z]{3,30}";
				break;
			case "password":
				$pattern = "(?=^.{8,35}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$";
				break;
			case "email":
				$pattern = ".+@.+\.+[\w]+";
				break;
			case "url":
				$pattern = "(http[s]?://)?[\S]+\.[\S]+";
				break;
			case "tel":
				$pattern = "([+]?[1-9][-]?)?((\([\d]{3}\))|(\d{3}[-]?))\d{3}[-]?\d{4}";
				break;
			case "number":
				$pattern = "\d+(\.\d{1,})?";
				break;
			case "color":
				$pattern = "#?([a-fA-F0-9]{6}|[a-fA-F0-9]{3})";
				break;
			case "date":
				$pattern = "((((0?)[1-9])|(1[0-2]))(-|/)(((0?)[1-9])|([1-2][\d])|3[0-1])(-|/)\d{4})|(\d{4}-(((0?)[1-9])|(1[0-2]))-(((0?)[1-9])|([1-2][\d])|3[0-1]))";
				break;
			case "time":
				$pattern = "(([0-1]?\d)|(2[0-3])):[0-5]\d";
				break;
			case 'datetime':
				$pattern = '(19|20)\d{2}-(0?[1-9]|1[12])-(0?[1-9]|[12]\d?|3[01]) T([01]\d|2[0-3])(:[0-5]\d)+';
				break;
			case "credit":
				$pattern = "\d{13,16}";
				break;
			default:
				$pattern = null;
		}
		return $pattern;
	}

	function today($timestamp = null) {							//Returns data as 'Y-m-d'. Defaults to current timestamp
		/**
		 * @depreciated
		 * @param [int $timestamp]
		 * @return string
		 */
		if(!$timestamp) $timestamp = time();
		$date = date('Y-m-d', $timestamp);
		return $date;
	}

	function long_date($timestamp = null) {						//Returns long-date format for timestamp
		/**
		 * @depreciated
		 * @param [int $timestamp]
		 * @return string
		 */
		if(!$timestamp) $timestamp = time();
		$date = date('l, F jS Y, g:i A', $timestamp);
		return $date;
	}

	function utf($string) {										//Concerts characters to UTF-8. Replaces special chars.
		return htmlentities($string, ENT_QUOTES | ENT_HTML5,"UTF-8");
	}
	function http_status($code = 200) {							//HTTP status header, the easy way. Just need status code
		/**
		 * @params integer $code
		 * @return void
		 * @link http://www.w3schools.com/tags/ref_httpmessages.asp
		 */
		switch($code) {
			case 100:
				$desc = 'Continue';
				break;
			case 101:
				$desc = 'Switching Protocols';
				break;
			case 103:
				$desc = 'Checkpoint';
				break;
			case 200:
				$desc = 'OK';
				break;
			case 201:
				$desc = 'Created';
				break;
			case 202:
				$desc = 'Accepted';
				break;
			case 203:
				$desc = 'Non-Authoritative Information';
				break;
			case 204:
				$desc = 'No Content';
				break;
			case 205:
				$desc = 'Reset Content';
				break;
			case 206:
				$desc = 'Partial Content';
				break;
			case 300:
				$desc = 'Multiple Choices';
				break;
			case 301:
				$desc = 'Moved Permanently';
				break;
			case 302:
				$desc = 'Found';
				break;
			case 303:
				$desc = 'See Other';
				break;
			case 304:
				$desc = 'Not Modified';
				break;
			case 306:
				$desc = 'Switch Proxy';
				break;
			case 307:
				$desc = 'Temporary Redirect';
				break;
			case 308:
				$desc = 'Resume Incomplete';
				break;
			case 400:
				$desc = 'Bad Request';
				break;
			case 401:
				$desc = 'Unauthorized';
				break;
			case 402:
				$desc = 'Payment Required';
				break;
			case 403:
				$desc = 'Forbidden';
				break;
			case 404:
				$desc = 'Not Found';
				break;
			case 405:
				$desc = 'Method Not Allowed';
				break;
			case 406:
				$desc = 'Not Acceptable';
				break;
			case 407:
				$desc = 'Proxy Authentication Required';
				break;
			case 408:
				$desc = 'Request Timeout';
				break;
			case 409:
				$desc = 'Conflict';
				break;
			case 410:
				$desc = 'Gone';
				break;
			case 411:
				$desc = 'Length Required';
				break;
			case 412:
				$desc = 'Precondition Failed';
				break;
			case 413:
				$desc = 'Request Entity Too Large';
				break;
			case 414:
				$desc = 'Request-URI Too Long';
				break;
			case 415:
				$desc = 'Unsupported Media Type';
				break;
			case 416:
				$desc = 'Requested Range Not Satisfiable';
				break;
			case 417:
				$desc = 'Expectation Failed';
				break;
			case 500:
				$desc = 'Internal Server Error';
				break;
			case 501:
				$desc = 'Not Implemented';
				break;
			case 502:
				$desc = 'Bad Gateway';
				break;
			case 503:
				$desc = 'Service Unavailable';
				break;
			case 504:
				$desc = 'Gateway Timeout';
				break;
			case 505:
				$desc = 'HTTP Version Not Supported';
				break;
			case 511:
				$desc = 'Network Authentication Required';
				break;
			default:
				http_status(500);
				return;
		}
		header("HTTP/1.1 {$code} {$desc}");
		return;
	}

	function header_type($type) {							// Set content-type header.
		/**
		 * @params string $type
		 * @return void
		 */
		header("Content-Type: {$type}\n");
	}

	function inline_min($file = null) {						// Strips tabs and new lines.
		/**
		 * @params string $file
		 * @return string
		 */
		if(!is_null($file)) return preg_replace("/\t|\n/","", file_get_contents(BASE . "/$file"));
	}


	function array_to_obj($arr) {
		return (object) $arr;
	}

	function array_to_json($arr) {
		return json_encode($arr);
	}

	function obj_to_json($arr) {
		return json_encode($arr);
	}

	function json_to_obj($json){
		return json_decode($json);
	}

	function json_to_array($json){
		return json_decode($json, true);
	}

	function obj_to_array($obj) {
		return (array) $obj;
	}

	function xml_to_obj($xml) {
		return simplexml_load_string($xml);
	}

	function xml_to_json($xml){
		return json_encode($xml);
	}

	function date_taken($filename) {						//Get date-taken from photo data
		/**
		 * @depreciated
		 * @params string $filename
		 * @return string
		 */
		$exif_data = exif_read_data ($filename);
		if (!empty($exif_data['DateTimeOriginal'])) {
			$timestamp = to_timestamp(substr($exif_data['DateTimeOriginal'], 0, 10), substr($exif_data['DateTimeOriginal'], 11));
		}
		else if (!empty($exif_data['DateTime'])) {
			$timestamp = to_timestamp(substr($exif_data['DateTime'], 0, 10), substr($exif_data['DateTime'], 11));
		}
		else $timestamp = filemtime($filename);
		return $timestamp;
	}

	function to_timestamp($date, $time="00:00") {			//Gets date("Y-m-d") [and time("H:i")] and returns timestamp
		/**
		 * @depreciated
		 * @params string $date[, string $time]
		 * @return string
		 */
		$h = substr($time, 0, 2);
		$m = substr($time, 3, 2);
		$y = substr($date, 0, 4);
		$M = substr($date, 5, 2);
		$d = substr($date, 8, 2);
		$timestamp = mktime($h, $m, 0, $M, $d, $y);
		return $timestamp;
	}
?>
