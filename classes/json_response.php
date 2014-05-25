<?php
	/**
	 * Creates and sends a JSON encoded response for XMLHTTPRequests
	 * Optimized to be handled by handleJSON in functions.js
	 * 
	 * @author Chris Zuber <shgysk8zer0@gmail.com>
	 * @copyright 2014, Chris Zuber
	 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3 (GPL-3.0)
	 * @package core_shared
	 * @version 2014-04-19
	 * 
	 * @example $resp = new json_response();
	 * $resp->notify(...)->html(...)->append(...)->prepend(...)->before(...)->after(...)->attributes(...)->remove(...)->send();
	 */

	class json_response {
		protected $response = [];
		
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
			 * @example "$resp->key = $value"
			 */

			$this->response[$key] = $value;
		}

		public function __get($key) {
			/**
			 * The getter method for the class.
			 *
			 * @param string $key
			 * @return mixed
			 * @example "$resp->key" Returns $value
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
			 * @example "isset({$resp->key})"
			 */

			return array_key_exists($key, $this->data);
		}

		public function __unset($key) {
			/**
			 * Removes an index from the array.
			 *
			 * @param string $key
			 * @return void
			 * @example "unset($resp->key)"
			 */

			unset($this->response[$key]);
		}

		public function __call($name, $arguments) {
			/**
			 * Chained magic getter and setter
			 * @param string $name, array $arguments
			 * @example "$resp->[getName|setName]($value)"
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
		
		public function text($selector, $content) {
			/**
			 * Sets textContent of elements matching $selector to $content
			 * 
			 * @param string $selector
			 * @param string $content
			 * 
			 */
			
			$this->response['text'][$selector] = $content;
			return $this;
		}
		
		public function notify($title = null, $body = null, $icon = null) {
			/**
			 * @param string $title
			 * @param string $body
			 * @param string $icon
			 * @usage $resp->notify('Title', 'Body', 'path/to/icon.png');
			 */
			
			$this->response['notify'] = [];
			if(isset($title)) $this->response['notify']['title'] = $title;
			if(isset($body)) $this->response['notify']['body'] = $body;
			if(isset($icon)) $this->response['notify']['icon'] = $icon;
			return $this;
		}
		
		public function html($selector, $content) {
			/**
			 * @param string $selector
			 * @param string $content
			 * @usage $resp->html('.cssSelector', '<p>Some HTML content</p>');
			 */
			if(!array_key_exists('html', $this->response)) $this->response['html'] = [];
			$this->response['html'][$selector] = $content;
			return $this;
		}
		
		public function append($selector, $content) {
			/**
			 * @param string $selector
			 * @param string $content
			 * @usage $resp->append('.cssSelector', '<p>Some HTML content</p>');
			 */
			
			if(!array_key_exists('append', $this->response)) $this->response['append'] = [];
			$this->response['append'][$selector] = $content;
			return $this;
		}
		
		public function prepend($selector, $content) {
			/**
			 * @param string $selector
			 * @param string $content
			 * @usage $resp->prepend('.cssSelector', '<p>Some HTML content</p>');
			 */
			
			if(!array_key_exists('prepend', $this->response)) $this->response['prepend'] = [];
			$this->response['prepend'][$selector] = $content;
			return $this;
		}
		
		public function before($selector, $content) {
			/**
			 * @param string $selector
			 * @param string $content
			 * @usage $resp->before('.cssSelector', '<p>Some HTML content</p>');
			 */
			
			if(!array_key_exists('before', $this->response)) $this->response['before'] = [];
			$this->response['before'][$selector] = $content;
			return $this;
		}
		
		public function after($selector, $content) {
			/**
			 * @param string $selector
			 * @param string $content
			 * @usage $resp->after('.cssSelector', '<p>Some HTML content</p>');
			 */
			
			$this->response['after'][$selector] = $content;
			return $this;
		}
		
		public function addClass($selector, $classes) {
			/**
			 * @param string $selector
			 * @param string $classes
			 * @usage $resp->addClass('.cssSelector', 'newClass, otherClass');
			 */
			
			$this->response['addClass'][$selector] = $classes;
			return $this;
		}
		
		public function removeClass($selector, $classes) {
			/**
			 * @param string $selector
			 * @param string $classes
			 * @usage $resp->removeClass('.cssSelector', 'someClass, someOtherClass');
			 */
			
			$this->response['removeClass'][$selector] = $classes;
			return $this;
		}
		
		public function remove($selector) {
			/**
			 * @param string $selector
			 * @usage $resp->remove('html .class > #id');
			 */
			
			(array_key_exists('remove', $this->response)) ? $this->response['remove'] .= ',' . $selector : $this->response['remove'] = $selector;
			return $this;
		}
		
		public function attributes($selector, $attribute, $value) {
			/**
			 * @param string $selector
			 * @param string $attribute
			 * @param mixed $value
			 * @usage $resp->attributes(
			 * 	'html', 'contextmenu', false
			 * )->attributes(
			 * 	'html', 'data-menu', 'admin'
			 * );
			 */
			
			$this->response['attributes'][$selector][$attribute] = $value;
			return $this;
		}
		
		public function script($js) {
			/**
			 * handleJSON in functions.js will eval() $js
			 * Requires 'unsafe-eval' be set on script-src in csp.ini
			 * which is generally a BAD idea.
			 * Including because it is useful.
			 * *USE WITH CAUTION* and watch your quotes
			 * 
			 * @param string $js (script to execute)
			 * @usage $resp->script("alert('Hello world')");
			 */
			
			(array_key_exists('script', $this->response)) ? $this->response['script'] .= ';' . $js : $this->response['script'] = $js;
			return $this;
		}
		
		public function sessionStorage($key, $value) {
			$this->response['sessionStorage'][$key] = $value;
			return $this;
		}
		
		public function localStorage($key, $value) {
			$this->response['localStorage'][$key] = $value;
			return $this;
		}
		
		public function log() {
			/**
			 * handleJSON in functions.js will console.log functions arguments
			 * 
			 * @param mixed (arguments passed to function)
			 * @usage $resp->log($session->nonce, $_SERVER['SERVER_NAME']);
			 */

			$this->response['log'] = func_get_args();
			return $this;
		}
		
		public function error() {
			/**
			 * handleJSON in functions.js will console.error functions arguments
			 * 
			 * @param mixed (arguments passed to function)
			 * @usage $resp->error($error);
			 */

			$this->response['error'] = func_get_args();
			return $this;
		}
		
		public function debug($format = false) {
			/**
			 * @param boolean $format
			 * @usage $resp->debug((true|false)?);
			 */
			
			if($format) {
				echo json_encode($this->response);
			}
			else {
				debug($this);
			}
		}
		
		public function send($key = null) {
			/**
			 * @param $key
			 * @usage $resp->sned() or $resp->send('notify')
			 */
			
			header('Content-Type: application/json');
			(isset($key)) ? exit(json_encode([$key => $this->response[$key]])) : exit(json_encode($this->response));
		}
	}
?>
