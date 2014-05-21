<?php
	class json_response {
		public $response = [];
		
		public function __construct($arr = null) {
			if(is_array($arr)) {
				$this->response = $arr;
			}
		}
		
		public function __set($key, $value) {
			/**
			 * Setter method for the class.
			 *
			 * @param string $key, mixed $value
			 * @return void
			 * @example "$storage->key = $value"
			 */

			$this->response[$key] = $value;
		}

		public function __get($key) {
			/**
			 * The getter method for the class.
			 *
			 * @param string $key
			 * @return mixed
			 * @example "$storage->key" Returns $value
			 */

			if(array_key_exists($key, $this->response)) {
				return $this->response[$key];
			}
			return false;
		}

		public function __isset($key) {
			/**
			 * @param string $key
			 * @return boolean
			 * @example "isset({$storage->key})"
			 */

			return array_key_exists($key, $this->data);
		}

		public function __unset($key) {
			/**
			 * Removes an index from the array.
			 *
			 * @param string $key
			 * @return void
			 * @example "unset($storage->key)"
			 */

			unset($this->response[$key]);
		}

		public function __call($name, $arguments) {
			/**
			 * Chained magic getter and setter
			 * @param string $name, array $arguments
			 * @example "$storage->[getName|setName]($value)"
			 */

			$name = strtolower($name);
			$act = substr($name, 0, 3);
			$key = substr($name, 3);
			switch($act) {
				case 'get':
					if(array_key_exists($key, $this->response)) {
						return $this->response[$key];
					}
					else{
						return false;
					}
					break;
				case 'set':
					$this->response[$key] = $arguments[0];
					return $this;
					break;
			}
		}
		
		public function notify($title = null, $body = null, $icon = null) {
			$this->response['notify'] = [];
			if(isset($title)) $this->response['notify']['title'] = $title;
			if(isset($body)) $this->response['notify']['body'] = $body;
			if(isset($icon)) $this->response['notify']['icon'] = $icon;
			return $this;
		}
		
		public function html($selector, $content) {
			if(!array_key_exists('html', $this->response)) $this->response['html'] = [];
			$this->response['html'][$selector] = $content;
			return $this;
		}
		
		public function append($selector, $content) {
			if(!array_key_exists('append', $this->response)) $this->response['append'] = [];
			$this->response['append'][$selector] = $content;
			return $this;
		}
		
		public function prepend($selector, $content) {
			if(!array_key_exists('prepend', $this->response)) $this->response['prepend'] = [];
			$this->response['prepend'][$selector] = $content;
			return $this;
		}
		
		public function before($selector, $content) {
			if(!array_key_exists('before', $this->response)) $this->response['before'] = [];
			$this->response['before'][$selector] = $content;
			return $this;
		}
		
		public function after($selector, $content) {
			$this->response['after'][$selector] = $content;
			return $this;
		}
		
		public function addClass($selector, $classes) {
			$this->response['addClass'][$selector] = $classes;
			return $this;
		}
		
		public function removeClass($selector, $classes) {
			$this->response['removeClass'][$selector] = $classes;
			return $this;
		}
		
		public function remove($selector) {
			(array_key_exists('remove', $this->response)) ? $this->response['remove'] .= ',' . $selector : $this->response['remove'] = $selector;
			return $this;
		}
		
		public function attributes($selector, $attribute, $value) {
			if(!array_key_exists('attributes', $this->response)) $this->response['attributes'] = [];
			$this->response['attributes'][$selector][$attribute] = $value;
			return $this;
		}
		
		public function debug() {
			echo json_encode($this->response);
		}
		
		public function send() {
			header('Content-Type: application/json');
			exit(json_encode($this->response));
		}
	}
?>