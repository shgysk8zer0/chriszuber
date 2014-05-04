<?php
	class pages {
		private static $instance = null;
		private $pdo, $data = [];
		public $content, $keywords, $description, $title, $author, $author_url, $created, $url, $kind;

		public static function load() {
			/**
			 * Static load function avoids creating multiple instances/connections
			 * It checks if an instance has been created and returns that or a new instance
			 *
			 * @params void
			 * @return posts object/class
			 * @example $session = session::load([$site])
			 */

			if(is_null(self::$instance)) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		public function __construct() {
			$this->pdo = _pdo::load();
			$this->path = explode('/', substr($_SERVER['REDIRECT_URL'], 1));
			switch(strtolower($this->path[0])) {
				case '':
					$this->kind = 'home';
					$results = $this->pdo->fetch_array('SELECT * FROM `posts` ORDER BY `created` LIMIT 1', 0);
					$this->title = $results->title;
					$this->content = $results->content;
					$this->keywords = explode(',', $results->keywords);
					$this->description = $results->description;
					$this->author = $results->author;
					$this->author_url = $results->url;
					$this->created = $results->created;
					$this->url = $results->url;
					break;
				case 'posts':
					$this->kind = 'posts';
					$results = $this->pdo->prepare('SELECT * FROM `posts` WHERE `title` = :title LIMIT 1')->bind(['title' => $path[1]])->execute()->get_results(0);
					$this->title = $results->title;
					$this->content = $results->content;
					$this->keywords = explode(',', $results->keywords);
					$this->description = $results->description;
					$this->author = $results->author;
					$this->author_url = $results->url;
					$this->created = $results->created;
					$this->url = $results->url;
					break;
				case 'tags':
					$this->kind = 'tags';
					$results = $this->pdo->prepare("SELECT * FROM `posts` WHERE `keywords` LIKE :tag")->bind(['tag' => "{$path[1]}"])->execute()->get_results();
					break;
				default:
					$this->kind = $this->path[0];
					$results = $this->pdo->prepare("SELECT * FROM :table WHERE `url` = :url")->bind(['table' => $post[0], 'url' => $post[1]])->execute()->get_results(0);
			}
			/*if($this->path[0] !== '') {
				$this->kind = 'home';
				$results = $this->pdo->fetch_array('SELECT * FROM `posts` ORDER BY `created` LIMIT 1', 0);
			}
			elseif(strtolower($this->path[0]) === 'tags') {
				$this->kind = 'tags';
				$results = $this->pdo->prepare("SELECT * FROM `tags` WHERE `keywords` LIKE :tag")->bind(['tag' => $post[1]])->execute()->get_results();
			}
			else {
				$results = $this->pdo->prepare("SELECT * FROM :table WHERE `url` = :url")->bind(['table' => $post[0], 'url' => $post[0]])->execute()->get_results(0);
			}*/
		}

		private function build($results) {
			$this->title = $results->title;
			$this->content = $rseults->content;
			$this->keywords = $results->keywords;
			$this->description = $results->description;
			$this->author = $results->author;
			debug($results);
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