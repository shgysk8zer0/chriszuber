<?php
	/**
	 * Wrapper for standard PDO class.
	 *
	 * This class is meant only to be extended and
	 * not used directly. It offers only a protected
	 * __construct method and a public escape.
	 *
	 * @author Chris Zuber <shgysk8zer0@gmail.com>
	 * @copyright 2014, Chris Zuber
	 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3 (GPL-3.0)
	 * @package core_shared
	 * @version 2014-08-27
	*/

	abstract class pdo_resources implements magic_methods {
		public $connected;
		protected $pdo, $data = [];
		abstract public static function load($con);

		protected function __construct($con = 'connect') {
			/**
			 * @method __construct
			 * @desc
			 * Gets database connection info from /connect.ini (using ini::load)
			 * The default ini file to use is connect, but can be passed another
			 * in the $con argument.
			 *
			 * Uses that data to create a new PHP Data Object
			 *
			 * @param string $con (.ini file to use for database credentials)
			 * @return void
			 * @example parent::__construct($con)
			 */

			$this->pdo = (is_string($con)) ? pdo_connect::load($con) : new pdo_connect($con);
			$this->connected = $this->pdo->connected;
		}

		public function __set($key, $value) {
			/**
			 * @method __set
			 * Setter method for the class.
			 *
			 * @param string $key
			 * @param mixed $value
			 * @return void
			 * @example "$pdo->key = $value"
			 */

			$key = str_replace(' ', '-', (string)$key);
			$this->data[$key] = $value;
		}

		public function __get($key) {
			/**
			 * The getter method for the class.
			 *
			 * @param string $key
			 * @return mixed
			 * @example "$pdo->key" Returns $value
			 */

			$key = str_replace(' ', '-', (string)$key);
			if(array_key_exists($key, $this->data)) {
				return $this->data[$key];
			}
			return false;
		}

		public function __isset($key) {
			/**
			 * @param string $key
			 * @return boolean
			 * @example "isset({$pdo->key})"
			 */

			return array_key_exists(str_replace(' ', '-', $key), $this->data);
		}

		public function __unset($key) {
			/**
			 * Removes an index from the array.
			 *
			 * @param string $key
			 * @return void
			 * @example "unset($pdo->key)"
			 */

			unset($this->data[str_replace(' ', '-', $key)]);
		}

		public function __call($name, array $arguments) {
			/**
			 * Chained magic getter and setter
			 * @param string $name, array $arguments
			 * @example "$pdo->[getName|setName]($value)"
			 */

			$name = strtolower((string)$name);
			$act = substr($name, 0, 3);
			$key = str_replace(' ', '-', substr($name, 3));
			switch($act) {
				case 'get': {
					if(array_key_exists($key, $this->data)) {
						return $this->data[$key];
					}
					else{
						return false;
					}
				} break;
				case 'set': {
					$this->data[$key] = $arguments[0];
					return $this;
				} break;
				default: {
					throw new Exception("Unknown method: {$name} in " . __CLASS__ .'->' . __METHOD__);
				}
			}
		}

		public function keys() {
			/**
			 * Show all keys for entries in $this->data array
			 *
			 * @param void
			 * @return array
			 */

			return array_keys($this->data);
		}

		public function escape(&$val) {
			/**
			 * For lack of a PDO escape, use quote, trimming off the quotations
			 *
			 * @param mixed $str
			 * @return mixed
			 */

			if(is_string($val)) {
				$val = preg_replace('/^\'|\'$/', null, $this->pdo->quote($val));
			}
			elseif(is_array($val)) {
				array_walk($val, [$this, 'escape']);
			}
			return $val;
		}

		public function quote(&$str) {
			/**
			 * For lack of a good ol' escape method in PDO.
			 *
			 * @param string $str
			 * @return string
			*/
			$str = $this->pdo->quote((string)$str);
			return $str;
		}

		public function columns(array $arr) {
			/**
			 * Converts array_keys to something safe for
			 * queries. Returns an array of the converted keys
			 *
			 * @param array $arr
			 * @return array
			 */

			$keys = array_keys($arr);
			$this->escape($keys);
			return join(', ', array_map(function($key){
				return "`{$key}`";
			}, $keys));
		}

		public function prepare_keys(array $arr) {
			/**
			 * Converts array_keys to something safe for
			 * queries. Returns the same array with converted keys
			 *
			 * @param array $arr
			 * @return array
			 */

			$keys = array_keys($arr);
			$this->escape($keys);
			return array_map(function($key) {
				return ':' . preg_replace('/\s/', '_', $key);
			}, $keys);
		}

		public function bind_keys(array $arr) {
			$keys = array_keys($arr);
			$this->escape($keys);
			return array_map(function($key) {
				return preg_replace('/\s/', '_', $key);
			}, $keys);
		}

		public function restore($fname = null) {
			return $this->pdo->restore($fname);
		}

		public function dump($filename = null) {
			return $this->pdo->dump($filename);
		}

		public function show_tables() {
			/**
			 * Returns a 0 indexed array of tables in database
			 *
			 * @param void
			 * @return array
			 */

			$query = "SHOW TABLES";
			$results = $this->pdo->query($query);
			$tables = $results->fetchAll(PDO::FETCH_COLUMN, 0);
			return $tables;
		}

		public function show_databases() {
			/**
			 * Returns a 0 indexed array of tables in database
			 *
			 * @param void
			 * @return array
			 */

			$query = 'SHOW DATABASES';
			$results = $this->pdo->query($query);
			$databases = $results->fetchAll(PDO::FETCH_COLUMN, 0);
			return $databases;
		}

		public function describe($table = null) {
			/**
			 * Describe $table, including:
			 * Field {name}
			 * Type {varchar|int... & (length)}
			 * Null (boolean)
			 * Default {value}
			 * Extra {auto_increment, etc}
			 *
			 * @param string $table
			 * @return array
			 */

			return $this->pdo->query("DESCRIBE `{$this->escape($table)}")->fetchAll(PDO::FETCH_CLASS);
		}

		public function columns_from(array $array) {
			/**
			 * Converts array keys into MySQL columns
			 * [
			 * 	'user' => 'me',
			 * 	'password' => 'password'
			 * ]
			 * becomes '`user`, `password`'
			 *
			 * @param array $array
			 * @return string
			 */

			$keys = array_keys($array);
			$key_walker = function(&$key) {
				$this->escape($key);
				$key = "`{$key}`";
			};
			array_walk($keys, $key_walker);

			return join(', ', $keys);
		}
	}

?>
