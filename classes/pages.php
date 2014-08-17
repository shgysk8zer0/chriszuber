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
						'tag' => preg_replace('/\s*/', '%', " {$this->path[1]} ")
					])->execute()->get_results();
				} break;

				case 'forms': {
					$this->type = 'forms';
					$this->get_content();
				} break;

				case 'posts': default: {
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
			$DB = _pdo::load();

			switch($this->type) {
				case 'posts': {
					$template = template::load('posts');
					$comments = template::load('comments');
					$time = new simple_date($this->data->created);

					foreach(explode(',', $this->data->keywords) as $tag) {
						$template->tags .= '<a href="' . URL . '/tags/' . urlencode(trim($tag)) . '" rel="tag">' . trim($tag) . "</a>";
					}
					$template->title(
						$this->data->title
					)->content(
						$this->data->content
					)->author(
						$this->data->author
					)->author_url(
						$this->data->author_url
					)->date(
						$time->out('m/d/Y')
					)->datetime(
						$time->out()
					)->home(
						URL
					)->comments(
						''
					)->url(
						$this->data->url
					);

					foreach($DB->prepare("
						SELECT
							`comment`,
							`author`,
							`author_url`,
							`time`
						FROM `comments`
						WHERE `post` = :post
					")->bind([
						'post' => $this->data->url
					])->execute()->get_results() as $comment) {
						$time = new simple_date($comment->time);
						$template->comments .= $comments->comment(
							$comment->comment
						)->author(
							(strlen($comment->author_url)) ? "<a href=\"{$comment->author_url}\" target=\"_blank\">{$comment->author}</a>" : $comment->author
						)->time(
							$time->out('l, F jS Y h:i A')
						)->out();
					}

					$this->content = $template->out();

				} break;

				case 'tags': {
					$this->content = '<div class="tags">';

					$template = template::load('tags');

					foreach($this->data as $post) {
						$datetime = new simple_date($post->created);

						$this->content .= $template->title(
							$post->title
						)->description(
							$post->description
						)->author(
							$post->author
						)->author_url(
							$post->author_url
						)->url(
							($post->url === '')? URL : URL .'/posts/' . $post->url
						)->date(
							$datetime->out('D M jS, Y \a\t h:iA')
						)->out();
					}
					$this->content .= '</div>';
				} break;
			}
		}

		public function debug() {
			debug($this);
		}
	}
?>