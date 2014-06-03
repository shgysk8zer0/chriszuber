<?php
	class pages {
		private static $instance = null;
		private $data, $path, $url, $status;
		public $content, $type;

		public static function load($url = null) {
			if(is_null(self::$instance)) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		public function __construct($url = null) {
			$connect = ini::load('connect');
			$this->status = (array_key_exists('REDIRECT_STATUS', $_SERVER)) ? $_SERVER['REDIRECT_STATUS'] : http_response_code();
			$pdo = _pdo::load();
			if(isset($url)) {
				$this->url = $url;
			}
			else {
				$this->url = (array_keys_exist('REDIRECT_URL', 'REDIRECT_STATUS', $_SERVER)) ? $_SERVER['REDIRECT_URL'] : $_SERVER['REQUEST_URI'];
			}
			$this->path = explode('/', urldecode(preg_replace('/^(' . preg_quote(URL, '/')  .')?(' .preg_quote($connect->site, '/') . ')?(\/)?/', null, strtolower($this->url))));

			switch($this->path[0]) {
				case 'tags': {
					$this->type = 'tags';
					$this->data = $pdo->prepare("
						SELECT `title`, `description`, `author`, `author_url`, `url`, `created`
						FROM `posts`
						WHERE `keywords` LIKE :tag
						LIMIT 20
					")->bind([
						//'tag' => "%{$this->path[1]}%"
						'tag' => preg_replace('/\w*/', '%', " {$this->path[1]} ")
					])->execute()->get_results();
				} break;

				default: {
					$this->type = 'posts';
					if(count($this->path) === 1){
						$this->data = $pdo->fetch_array('
							SELECT *
							FROM `posts`
							WHERE `url` = ""
							LIMIT 1
						', 0);
					}
					else {
						$this->data = $pdo->prepare('
							SELECT *
							FROM `posts`
							WHERE `url` = :url
							ORDER BY `created`
							LIMIT 1
						')->bind([
							'url' => urlencode($this->path[1])
						])->execute()->get_results(0);
					}
				}
			}
			if($this->data) $this->get_content();
		}

		public function __get($key) {
			return $this->data->$key;
		}

		public function get_content() {
			$login = login::load();
			switch($this->type) {
				case 'posts': {
					$template = template::load('posts');
					$time = new simple_date($this->data->created);
					$keywords = explode(',', $this->data->keywords);
					$tags = [];
					//foreach($keywords as $tag) $tags[] = '<a href="' . URL . '/tags/' . trim(strtolower(preg_replace('/\s/', '-', trim($tag)))) . '">' . trim(caps($tag)) . "</a>";
				foreach($keywords as $tag) $tags[] = '<a href="' . URL . '/tags/' . strtolower(urlencode(trim($tag))) . '">' . trim($tag) . "</a>";
					$this->content = $template->set([
						'title' => $this->data->title,
						'tags' => join(PHP_EOL, $tags),
						'content' => $this->data->content,
						'author' => $this->data->author,
						'author_url' => $this->data->author_url,
						'date' => $time->out('m/d/Y'),
						'datetime' => $time->out()
					])->out();
				} break;

				case 'tags': {
					$this->content = '<div class="tags">';

					$template = template::load('tags');

					foreach($this->data as $post) {
						$datetime = new simple_date($post->created);
						$this->content .= $template->set([
							'title' => $post->title,
							'description' => $post->description,
							'author' => $post->author,
							'author_url' => $post->author_url,
							'url' => ($post->url === '')? URL : URL .'/posts/' . $post->url,
							'date' => $datetime->out('D M jS, Y \a\t h:iA')
						])->out();
					}
					$this->content .= '</div>';
				}
			}
		}

		public function debug() {
			debug($this);
		}
	}
?>