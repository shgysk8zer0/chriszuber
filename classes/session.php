<?php
/**
* Since this class is using $_SESSION for all data, there are few variables
* There are several methods to make better use of $_SESSION, and it adds the ability to chain
* As $_SESSION is used for all storage, there is no pro or con to using __construct vs ::load()
*/
	class session{											#Just a class for interacting differently with $_SESSION
		private $name;
		private static $instance = null;
		
		public static function start($site = null) {		#Static method for loading the class
		if(is_null(self::$instance)) {
			self::$instance = new self($site);
		}
		return self::$instance;
	}
		
		public function __construct($name = null) {			#Creates new instance of session. $name is optional, and sets session_name if session has not been started
			if(!isset($_SESSION)) {							#Do not create new session of one has already been created
				if(isset($name)) {
					$name = trim(strtolower($name));
					session_name($name);
					$this->name = $name;
					session_start();
				}
				else {										#If session has already started, get the name of it
					$this->name = session_name();
				}
			}
		}
		
		public function __get($key) {						#Class getter. Called as "$this->key" and returns that value
			$key = strtolower(preg_replace('/_/', '-', $key));
			if(array_key_exists($key, $_SESSION)) {
				return $_SESSION[$key];
			}
			return false;
		}
		
		public function __set($key, $value) {				#Class setter. Called as "$this->key = $value".
			$key = strtolower(preg_replace('/_/', '-', $key));
			$_SESSION[$key] = trim($value);
		}
		
		public function __call($name, $arguments) {			#Magic getters and setters. Called as $this->[setName|getName]($value)
			$name = strtolower($name);
			$act = substr($name, 0, 3);
			$key = preg_replace('/_/', '-', substr($name, 3));
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
		
		public function __isset($key) {					#Returns a boolean. Called as isset($this->key)
			$key = strtolower(preg_replace('/_/', '-', $key));
			return array_key_exists($key, $_SESSION);
		}

		public function __unset($key) {					#Removes an entry. Called as unset($this->key)
			$key = strtolower(preg_replace('/_/', '-', $key));
			unset($_SESSION[$key]);
		}
		
		public function destroy() {						#Destroys $_SESSION and attempts to destroy the associated cookie
			session_destroy();
			if(array_key_exists(session_name(), $_COOKIE)) setcookie("{$this->name}", '', time() - 3600);
		}
		
		public function debug() {						#Debuggining function. Prints out all data including data types and values
			echo '<pre><code>';
			print_r($_SESSION);
			echo '</code></pre>';
		}
	}
?>
