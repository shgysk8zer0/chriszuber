<?php
	class ini {
		private static $instance = [];
		private $data = [];

		public static function load($file, $multi = false) {
			if(!array_key_exists($file, self::$instance)) self::$instance[$file] = new self($file, $multi);
			return self::$instance[$file];
		}

		public function __construct($file, $multi = false) {
			$this->data = parse_ini_file("{$file}.ini", $multi);
		}
		
		public function __set($key, $value) {
			/**
			 * Setter method for the class.
			 *
			 * @param string $key, mixed $value
			 * @return void
			 * @example "$storage->key = $value"
			 */

			$key = preg_replace('/_/', '-', preg_quote($key, '/'));
			$this->data[$key] = $value;
		}

		public function __get($key) {
			/**
			 * The getter method for the class.
			 *
			 * @param string $key
			 * @return mixed
			 * @example "$storage->key" Returns $value
			 */

			$key = preg_replace('/_/', '-', preg_quote($key, '/'));
			if(array_key_exists($key, $this->data)) {
				return $this->data[$key];
			}
			return false;
		}

		public function __isset($key) {
			/**
			 * @param string $key
			 * @return boolean
			 * @example "isset({$storage->key})"
			 */

			return array_key_exists(preg_replace('/_/', '-', $key), $this->data);
		}

		public function __unset($index) {
			/**
			 * Removes an index from the array.
			 *
			 * @param string $key
			 * @return void
			 * @example "unset($storage->key)"
			 */

			unset($this->data[preg_replace('/_/', '-', $index)]);
		}

		public function __call($name, $arguments) {
			/**
			 * Chained magic getter and setter
			 * @param string $name, array $arguments
			 * @example "$storage->[getName|setName]($value)"
			 */

			$name = strtolower($name);
			$act = substr($name, 0, 3);
			$key = preg_replace('/_/', '-', substr($name, 3));
			switch($act) {
				case 'get':
				if(array_key_exists($key, $this->data)) {
					return $this->data[$key];
				}
				else{
					die('Unknown variable.');
				}
				break;
				case 'set':
				$this->data[$key] = $arguments[0];
				return $this;
				break;
				default:
				die('Unknown method.');
			}
		}

		public function keys() {
			/**
			 * Returns an array of all array keys for $thsi->data
			 *
			 * @param void
			 * @return array
			 */

			return array_keys($this->data);
		}
	}
?>
