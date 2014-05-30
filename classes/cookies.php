<?php
	class cookies {
		public $expires, $path, $domain, $secure, $httponly;
		
		public function __construct($expires = 0, $path = null, $domain = null, $secure = false, $httponly = false){
			$this->expires = (int) (preg_match('/^\d+$/', $expires)) ? $expires : $this->data = date_timestamp_get(date_create($expires));
			$this->path = (isset($path)) ? $path : preg_replace('/^' . preg_quote("{$_SERVER['REQUEST_SCHEME']}://{$_SERVER['SERVER_NAME']}", '/') . '/', '', URL);
			$this->domain = (isset($domain)) ? $domain : $_SERVER['HTTP_HOST'];
			$this->secure = (isset($secure)) ? $secure : false;
			$this->httponly = (isset($httponly)) ? $httponly : false;
		}
		
		public function __set($name, $value) {
			setcookie(preg_replace('/_/', '-', $name), $value, $this->expires, $this->path, $this->domain, $this->secure, $this->httponly);
			return $this;
		}
		
		public function __get($name) {
			$name = preg_replace('/_/', '-', $name);
			return (array_key_exists($name, $_COOKIE)) ? $_COOKIE[$name] : false;
		}
		
		public function __call($name, $arguments) {
			/**
			 * Chained magic getter and setter
			 * @param string $name, array $arguments
			 * @example "$storage->[getName|setName]($value)"
			 */

			$key = preg_replace('/_/', '-', substr(strtolower($name), 3));
			switch(substr($name, 0, 3)) {
				case 'get': {
					return (array_key_exists($key, $_COOKIE)) ? $_COOKIE[$key] : false;
				}break;
				case 'set':{
					setcookie($key, $arguments[0], (int)$this->expires, $this->path, $this->domain, $this->secure, $this->httponly);
					return $this;
				}break;
				default:
					exit('Unknown method.');
			}
		}
		
		public function __isset($name) {
			return array_key_exists(preg_replace('/_/', '-', $name), $_COOKIE);
		}
		
		public function keys() {
			return array_keys($_COOKIE);
		}
		
		public function __unset($name) {
			$name = preg_replace('/_/', '-', $name);
			if(array_key_exists($name, $_COOKIE)) {
				unset($_COOKIE[$name]);
				setcookie($name, null, -1, $this->path, $this->domain, $this->secure, $this->httponly);
				return true;
			}
			return false;
		}
	}
?>
