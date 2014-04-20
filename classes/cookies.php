<?php
	class cookies{
		
		private $expires;
		public function __construct($expires = null){
			(is_null($expires)) ? $this->expires = time() + 60 * 60 * 24 : $this->expires = $expires;
			$this->expires = $expires;
		}
		
		public function __set($key, $value) {
			setcookie($key, $value, $this->expires);
			return $this;
		}
		
		public function __get($key) {
			if(array_key_exists($key, $_COOKIE)) return $_COOKIE[$key];
		}
		
		public function destroy($cname) {
			if(array_key_exists($cname, $_COOKIE)) setcookie($cname, '', time() - 3600);
		}
	}
?>
