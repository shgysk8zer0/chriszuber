<?php
	class login extends _pdo {
		/**
		 * Class to handle login or create new users from form submissions or $_SESSION
		 *
		 * @author Chris Zuber <shgysk8zer0@gmail.com>
		 * @copyright 2014, Chris Zuber
		 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3 (GPL-3.0)
		 * @package core_shared
		 * @version 2014-04-19
		 * @uses /classes/_pdo.php
		 */

		public $user_data = array();
		private static $instance = null;

		public static function load() {
			/**
			 * Static load function avoids creating multiple instances/connections
			 * It checks if an instance has been created and returns that or a new instance
			 *
			 * @params void
			 * @return login object/class
			 * @example $login = _login::load
			 */

			if(is_null(self::$instance)) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		public function __construct() {
			/**
			 * Gets database connection info from /connect.ini (stored in $site)
			 * Uses that data to create a new PHP Data Object
			 *
			 * @param void
			 * @global $site
			 * @return void
			 * @example $login = new login()
			 */

			parent::__construct();					#login extends _pdo, so create new instance of parent.
			#[TODO] Use static parent::load() instead, but this causes errors

			$this->user_data = array(
				'user' => null,
				'password' => null,
				'role' => null,
				'logged-in' => false
			);
		}

		public function create_from($source) {
			/**
			 * Creates new user using an array passed as source. Usually $_POST or $_SESSION
			 *
			 * @param array $source
			 * @return void
			 * @example $login->create_from($_POST|$_GET|$_REQUEST|array())
			 */

			if(array_keys_exist('user', 'password', $source)) {	#Check if needed entries exist in array
				if(array_key_exists('repeat', $source) and $source['password'] !== $source['repeat']) die("<code class=\"error\">Passwords do not match</code>");
				$salt = mcrypt_create_iv(50, MCRYPT_DEV_URANDOM);
				$options = [
						'cost' => 11,
						'salt' => $salt
				];

				$this->array_insert('users', [
					'user' => $source['user'],
					'password' => password_hash(trim($source['password']), PASSWORD_BCRYPT, $options)
				]);
			}
		}

		public function login_with($source) {
			/**
			 * Intended to find login info from $_COOKIE, $_SESSION, or $_POST
			 *
			 * @param array $source
			 * @return void
			 * @example $login->login_with($_POST|$_GET|$_REQUEST|$_SESSION|array())
			 */

			#[TODO] Handle an invalid login
			if(array_keys_exist('user', 'password', $source)) {	#Make sure necessary information has been passed
				$query = "SELECT `user`, `password`, `role` FROM `users` WHERE `user` = '{$this->escape($source['user'])}' LIMIT 1";
				$results = $this->fetch_array($query)[0];
				if(password_verify(trim($source['password']), $results->password) and $results->role !== 'new') {	#Verifies by matching hashes
					$this->setUser($results->user)->setPassword($results->password)->setRole($results->role)->setLogged_In(true);
				}
			}
		}

		public function __set($key, $value) {
			/**
			 * Setter method for the class.
			 *
			 * @param string $key, mixed $value
			 * @return void
			 * @example "$login->key = $value"
			 */

			$key = preg_replace('/_/', '-', strtolower($key));
			$this->user_data[$key] = $value;
			return $this;
		}

		public function __get($key) {
			/**
			 * The getter method for the class.
			 *
			 * @param string $key
			 * @return mixed
			 * @example "$login->key" Returns $value
			 */

			$key = preg_replace('/_/', '-', strtolower($key));
			if(array_key_exists($key, $this->user_data)) {
				return $this->user_data[$key];
			}
			return false;
		}

		public function __isset($key) {
			/**
			 * @param string $key
			 * @return boolean
			 * @example "isset({$login->key})"
			 */

			$key = preg_replace('/_/', '-', strtolower($key));
			return array_key_exists($key, $this->user_data);
		}

		public function __unset($key) {
			/**
			 * Removes an index from the array.
			 *
			 * @param string $key
			 * @return void
			 * @example "unset($login->key)"
			 */

			$key = preg_replace('/_/', '-', strtolower($key));
			unset($this->user_data[$key]);
		}

		public function __call($name, $arguments) {
			/**
			 * Chained magic getter and setter
			 * @param string $name, array $arguments
			 * @example "$login->[getName|setName]($value)"
			 */

			$name = strtolower($name);
			$act = substr($name, 0, 3);
			$key = preg_replace('/_/', '-', substr($name, 3));
			switch($act) {
				case 'get':
					if(array_key_exists($key, $this->user_data)) {
						return $this->user_data[$key];
					}
					else{
						die('Unknown variable.');
					}
					break;
				case 'set':
					$this->user_data[$key] = $arguments[0];
					return $this;
					break;
				default:
					die('Unknown method.');
			}
		}

		public function logout() {
			/**
			 * Undo the login. Destroy it. Removes session and cookie. Sets logged_in to false
			 *
			 * @param void
			 * @return void
			 */

			if(isset($_COOKIE['cert'])) setcookie('cert', '', time()-3600);
			if(isset($_SESSION)) session_destroy();
			$this->setUser(null)->setPassword(null)->setRole(null)->setLogged_In(false);
		}

		public function debug() {
			/**
			 * Prints out class information using print_r
			 * wrapped in <pre> and <code>
			 *
			 * @param void
			 * @return void
			 */

			debug($this);
		}
	}
?>
