<?php
	/**
	 * Since this class is using $_SESSION for all data, there are few variables
	 * There are several methods to make better use of $_SESSION, and it adds the ability to chain
	 * As $_SESSION is used for all storage, there is no pro or con to using __construct vs ::load()
	 *
	 * @author Chris Zuber <shgysk8zer0@gmail.com>
	 * @package core_shared
	 * @version 2014-04-19
	 * @copyright 2014, Chris Zuber
	 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3 (GPL-3.0)
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
	 *
	 * @var string $name
	 * @var int? $expires
	 * @var string $path
	 * @var string $domain
	 * @var boolean $secure
	 * @var boolean $httponly
	 * @var session $instance
	*/

	namespace core;
	class session implements magic_methods {
		private $name, $expires, $path, $domain, $secure, $httponly;
		private static $instance = null;

		/**
		 * Static load function avoids creating multiple instances/connections
		 * It checks if an instance has been created and returns that or a new instance
		 *
		 * @params [string $site] optional name for session
		 * @return session object/class
		 * @example $session = session::load([$site])
		 */

		public static function load($site = null) {
			if(is_null(self::$instance)) {
				self::$instance = new self($site);
			}
			return self::$instance;
		}

		/**
		 * Creates new instance of session. $name is optional, and sets session_name if session has not been started
		 *
		 * @params [string $site] optional name for session
		 * @return void
		 * @example $session = new session([$site])
		 */

		public function __construct($name = null) {
			if(session_status() !== PHP_SESSION_ACTIVE) {							#Do not create new session of one has already been created
				$this->expires = 0;
				$this->path = '/' . trim(str_replace("{$_SERVER['REQUEST_SCHEME']}://{$_SERVER['SERVER_NAME']}", '/', URL), '/');
				$this->domain = $_SERVER['HTTP_HOST'];
				$this->secure = https();
				$this->httponly = true;

				if(is_null($name)) {
					$name = end(explode('/', trim(BASE, '/')));
				}
				$this->name = preg_replace('/[^\w]/', null, strtolower($name));
				session_name($this->name);
				if(!array_key_exists($this->name, $_COOKIE)) {
					session_set_cookie_params($this->expires, $this->path, $this->domain, $this->secure, $this->httponly);
				}
				session_start();
			}
		}

		/**
		 * The getter method for the class.
		 *
		 * @param string $key
		 * @return mixed
		 * @example "$session->key" Returns $value
		 */

		public function __get($key) {
			$key = strtolower(str_replace('_', '-', $key));
			if(array_key_exists($key, $_SESSION)) {
				return $_SESSION[$key];
			}
			return false;
		}

		/**
		 * Setter method for the class.
		 *
		 * @param string $key, mixed $value
		 * @return void
		 * @example "$session->key = $value"
		 */

		public function __set($key, $value) {
			$key = strtolower(str_replace('_', '-', $key));
			$_SESSION[$key] = trim($value);
		}

		/**
		 * Chained magic getter and setter
		 * @param string $name, array $arguments
		 * @example "$session->[getName|setName]($value)"
		 */

		public function __call($name, array $arguments) {
			$name = strtolower($name);
			$act = substr($name, 0, 3);
			$key = str_replace('_', '-', substr($name, 3));
			switch($act) {
				case 'get':
					if(array_key_exists($key, $_SESSION)) {
						return $_SESSION[$key];
					}
					else{
						return false;
					}
					break;
				case 'set':
					$_SESSION[$key] = $arguments[0];
					return $this;
					break;
				default:
					die('Unknown method.');
			}
		}

		/**
		 * @param string $key
		 * @return boolean
		 * @example "isset({$session->key})"
		 */

		public function __isset($key) {
			$key = strtolower(str_replace('_', '-', $key));
			return array_key_exists($key, $_SESSION);
		}

		/**
		 * Removes an index from the array.
		 *
		 * @param string $key
		 * @return void
		 * @example "unset($session->key)"
		 */

		public function __unset($key) {
			$key = strtolower(str_replace('_', '-', $key));
			unset($_SESSION[$key]);
		}

		/**
		* Destroys $_SESSION and attempts to destroy the associated cookie
		*
		* @param void
		* @return void
		*/

		public function destroy() {
			session_destroy();
			unset($_SESSION);
			if(array_key_exists($this->name, $_COOKIE)){
				unset($_COOKIE[$this->name]);
				setcookie($this->name, null, -1, $this->path, $this->domain, $this->secure, $this->httponly);
			}
		}

		/**
		 * Clear $_SESSION. All data in $_SESSION is unset
		 *
		 * @param void
		 * @example $session->restart()
		 */

		public function restart() {
			session_unset();
			return $this;
		}

		/**
		 * Prints out class information using print_r
		 * wrapped in <pre> and <code>
		 *
		 * @param void
		 * @return void
		 */

		public function debug() {
			debug($this);
		}
	}
?>
