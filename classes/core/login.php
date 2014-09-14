<?php
	/**
	 * Class to handle login or create new users from form submissions or $_SESSION
	 * Can check login role as well (new, user, admin, etc)
	 *
	 * @author Chris Zuber <shgysk8zer0@gmail.com>
	 * @package core_shared
	 * @version 2014-04-19
	 * @uses /classes/_pdo.php
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
	 * @var array $data
	 * @var login $instance
	 */

	namespace core;
	class login extends _pdo {
		public $data = [];
		protected static $instance = null;

		/**
		 * Static load function avoids creating multiple instances/connections
		 * It checks if an instance has been created and returns that or a new instance
		 *
		 * @param string $ini (ini file to use for database connection configuration)
		 * @return login object/class
		 * @example $login = _login::load
		 */

		public static function load($ini = 'connect') {
			if(is_null(self::$instance)) {
				self::$instance = new self($ini);
			}
			return self::$instance;
		}

		/**
		 * Gets database connection info from /connect.ini (stored in $site)
		 * Uses that data to create a new PHP Data Object
		 *
		 * @param string $ini (ini file to use for database connection configuration)
		 * @return void
		 * @example $login = new login()
		 * @todo Use static parent::load() instead, but this causes errors
		 */

		public function __construct($ini = 'connect') {
			parent::__construct($ini);	//login extends _pdo, so create new instance of parent.

			$this->data = array(
				'user' => null,
				'password' => null,
				'role' => null,
				'logged-in' => false
			);
		}

		/**
		 * Creates new user using an array passed as source. Usually $_POST or $_SESSION
		 *
		 * @param array $source
		 * @return boolean
		 * @example $login->create_from($_POST|$_GET|$_REQUEST|array())
		 */

		public function create_from(array $source) {
			if(array_keys_exist('user', 'password', $source)) {
				if(array_key_exists('repeat', $source) and $source['password'] !== $source['repeat']) return false;
				return $this->prepare("
					INSERT INTO `users` (
						`user`,
						`password`
					) VALUES (
						:user,
						:password
					)
				")->bind([
					'user' => trim($source['user']),
					'password' => password_hash(trim((string)$source['password']), PASSWORD_BCRYPT, [
						'cost' => 11,
						'salt' => mcrypt_create_iv(50, MCRYPT_DEV_URANDOM)
					])
				])->execute();
			}
			else {
				return false;
			}
		}

		/**
		 * Intended to find login info from $_COOKIE, $_SESSION, or $_POST
		 *
		 * @param array $source
		 * @return void
		 * @example $login->login_with($_POST|$_GET|$_REQUEST|$_SESSION|array())
		 */

		public function login_with(array $source) {
			if(array_keys_exist('user', 'password', $source)) {
				$results = $this->prepare("
					SELECT `user`,
					`password`,
					`role`
					FROM `users`
					WHERE `user` = :user
					LIMIT 1
				")->bind([
					'user' => $source['user']
				])->execute()->get_results(0);
				if(password_verify(
					trim($source['password']),
					$results->password
				) and $results->role !== 'new') {
					$this->setUser($results->user)->setPassword($results->password)->setRole($results->role)->setLogged_In(true);
				}
			}
			return ($this->data['logged-in']);
		}

		/**
		 * Undo the login. Destroy it. Removes session and cookie. Sets logged_in to false
		 *
		 * @param void
		 * @return void
		 */

		public function logout() {
			$this->setUser(null)->setPassword(null)->setRole(null)->setLogged_In(false);
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
