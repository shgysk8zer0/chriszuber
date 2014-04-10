<?php
	class storage{									#Class for simply holding data
	/**
	* Consists almost entirely of magic methods.
	* Functionality is similar to globals, except new entries may be made
	* and the class also has save/load methods for saving to or loading from $_SESSION
	* Uses a private array for storage, and magic methods for getters and setters
	*
	* I just prefer using $session->key over $_SESSION[key]
	* It also provides some chaining, so $session->setName(value)->setOtherName(value2)->getExisting() can be done
	*/
		private static $instance = null;
		private $data;
		
		public static function start() {					#The prefered method of using the class. Only useful as a static.
			if(is_null(self::$instance)) {
				self::$instance = new self;
			}
			return self::$instance;
		}
		
		public function __construct() {						#How I wish I could make this a private function... Do not use "new storage"
			$this->data = array();
		}
	
		public function __set($key, $value) {				#Setter method for the class. "$this->key = $value"
			$key = preg_replace('/_/', '-', $key);
			$this->data[$key] = $value;
		}

		public function __get($key) {						#The getter method for the class. "$this->key" Returrns $value
			$key = preg_replace('/_/', '-', $key);
			if(array_key_exists($key, $this->data)) {
				return $this->data[$key];
			}
			return false;
		}

		public function __isset($index) {					#Returns boolean. Called as "isset($this->index)"
			return array_key_exists(preg_replace('/_/', '-', $index), $this->data);
		}

		public function __unset($index) {					#Removes an index from the array. Called as "unset($this->index)"
			unset($this->data[preg_replace('/_/', '-', $index)]);
		}

		public function __call($name, $arguments) {			#Chained magic getter and setter. Called as $this->[getName|setName]($value)
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
	
		public function keys() {						#Returns an array of all values which have been set.
			return array_keys($this->data);
		}
	
		public function save() {						#Saves all $data to $_SESSION
			#[TODO] Make work with more types of data
			$_SESSION['storage'] = $this->data;
		}
	
		public function load() {						#Loads existing $data array from $_SESSION
			#[TODO] Make work with more types of data
			if(array_key_exists('storage', $_SESSION)) {
				$this->data = $_SESSION['storage'];
			}
		}
	
		public function clear() {
			unset($this->data);
			unset($this);
		}
	
		public function debug() {
			echo "<pre><code>";
			print_r($this);
			echo "</code></pre>";
		}
	}
?>
