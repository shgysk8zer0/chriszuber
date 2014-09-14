<?php
	/**
	 * Consists almost entirely of magic methods.
	 * Functionality is similar to globals, except new entries may be made
	 * and the class also has save/load methods for saving to or loading from $_SESSION
	 * Uses a private array for storage, and magic methods for getters and setters
	 *
	 * I just prefer using $session->key over $_SESSION[key]
	 * It also provides some chaining, so $session->setName(value)->setOtherName(value2)->getExisting() can be done
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
	 */

	namespace core;
	class storage {

		private static $instance = null;
		private $data;

		/**
		 * Static load function avoids creating multiple instances/connections
		 * It checks if an instance has been created and returns that or a new instance
		 *
		 * @params void
		 * @return storage object/class
		 * @example $storage = storage::load
		 */

		public static function load() {
			if(is_null(self::$instance)) {
				self::$instance = new self;
			}
			return self::$instance;
		}

		/**
		 * Creates new instance of storage.
		 *
		 * @params void
		 * @return void
		 * @example $storage = new storage
		 */

		public function __construct() {
			$this->data = array();
		}

		/**
		 * Setter method for the class.
		 *
		 * @param string $key, mixed $value
		 * @return void
		 * @example "$storage->key = $value"
		 */

		public function __set($key, $value) {
			$key = str_replace('_', '-', $key);
			$this->data[$key] = $value;
		}

		/**
		 * The getter method for the class.
		 *
		 * @param string $key
		 * @return mixed
		 * @example "$storage->key" Returns $value
		 */

		public function __get($key) {
			$key = str_replace('_', '-', $key);
			if(array_key_exists($key, $this->data)) {
				return $this->data[$key];
			}
			return false;
		}

		/**
		 * @param string $key
		 * @return boolean
		 * @example "isset({$storage->key})"
		 */

		public function __isset($key) {
			return array_key_exists(str_replace('_', '-', $key), $this->data);
		}

		/**
		 * Removes an index from the array.
		 *
		 * @param string $key
		 * @return void
		 * @example "unset($storage->key)"
		 */

		public function __unset($key) {
			unset($this->data[str_replace('_', '-', $key)]);
		}

		/**
		 * Chained magic getter and setter
		 * @param string $name, array $arguments
		 * @example "$storage->[getName|setName]($value)"
		 */

		public function __call($name, array $arguments) {
			$name = strtolower($name);
			$act = substr($name, 0, 3);
			$key = str_replace('_', '-', substr($name, 3));
			switch($act) {
				case 'get': {
					if(array_key_exists($key, $this->data)) {
						return $this->data[$key];
					}
				} break;
				case 'set': {
					$this->data[$key] = $arguments[0];
					return $this;
				} break;
			}
		}

		/**
		 * Returns an array of all array keys for $thsi->data
		 *
		 * @param void
		 * @return array
		 */

		public function keys() {
			return array_keys($this->data);
		}

		/**
		 * Saves all $data to $_SESSION
		 *
		 * @param void
		 * @return void
		 * @todo Make work with more types of data
		 */

		public function save() {
			$_SESSION['storage'] = $this->data;
		}

		/**
		 * Loads existing $data array from $_SESSION
		 *
		 * @param void
		 * @return void
		 * @todo Make work with more types of data
		 */

		public function restore() {
			if(array_key_exists('storage', $_SESSION)) {
				$this->data = $_SESSION['storage'];
			}
		}

		/**
		 * Destroys/clears/deletes
		 * This message will self destruct
		 *
		 * @param void
		 * @return void
		 */

		public function clear() {
			unset($this->data);
			unset($this);
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
