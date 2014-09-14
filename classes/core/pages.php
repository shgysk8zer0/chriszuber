<?php
	/**
	 * Easily work with pages by getting just the content/meta unique to them
	 * Works for either regular or AJAX requests
	 *
	 * @author Chris Zuber <shgysk8zer0@gmail.com>
	 * @package core_shared
	 * @version 2014-07-09
	 * @copyright 2014, Chris Zuber
	 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3 (GPL-3.0)
	 * This program is free software; you can redistribute it and/or
	 * modify it under the terms of the GNU General Public License
	 * as published by the Free Software Foundation, either version 3
	 * of the License, or (at your option) any later version.
	 *
	 * This program is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	 * GNU General Public License for more details.
	 *
	 * You should have received a copy of the GNU General Public License
	 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

	namespace core;
	class pages {
		private static $instance = null;
		private $data, $path, $url, $status, $parsed;
		public $content, $type;

		public static function load($url = null) {
			if(is_null(self::$instance)) {
				self::$instance = new self($url);
			}
			return self::$instance;
		}

		public function __construct($url = null) {
			$this->status = (array_key_exists('REDIRECT_STATUS', $_SERVER)) ? $_SERVER['REDIRECT_STATUS'] : http_response_code();
			$pdo =_pdo::load('connect');
			if(is_string($url)) {
				$this->url = $url;
			}
			else {
				$this->url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'];
				if(array_key_exists('REDIRECT_URL', $_SERVER)) {
					$this->url .= $_SERVER['REDIRECT_URL'];
				}
				elseif(array_key_exists('REQUEST_URI', $_SERVER)) {
					$this->url .= $_SERVER['REQUEST_URI'];
				}
			}

			$this->parsed = (object)parse_url(strtolower(urldecode($this->url)));
			$this->path = explode('/', trim($this->parsed->path, '/'));
			if(trim(BASE, '/') !== trim($_SERVER['DOCUMENT_ROOT'], '/')) {
				unset($this->path[0]);
				$this->path = array_values($this->path);
				if(empty($this->path)) {
					$this->path = [''];
				}
			}
			if($pdo->connected) {
				switch($this->path[0]) {
					case 'tags': {
						if(isset($this->path[1])) {
							$this->type = 'tags';
							$this->data = $pdo->prepare("
								SELECT `title`, `description`, `author`, `author_url`, `url`, `created`
								FROM `posts`
								WHERE `keywords` LIKE :tag
								LIMIT 20
							")->bind([
								'tag' => preg_replace('/\s*/', '%', " {$this->path[1]} ")
							])->execute()->get_results();
						}
					} break;

					case 'posts':
					case '/':
					case '': {
						$this->type = 'posts';
						if(count($this->path) === 1 and $this->path[0] === '') {
							$this->data = $pdo->fetch_array('
								SELECT *
								FROM `posts`
								WHERE `url` = ""
								LIMIT 1
							', 0);
						}
						elseif(count($this->path) >= 2) {
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
					} break;
				}
				if(isset($this->data) and !empty($this->data)) $this->get_content();

				else{
					$this->error_page();
				}
			}
		}

		public function __get($key) {
			return isset($this->data->$key) ? $this->data->$key : false;
		}

		public function __isset($key) {
			return isset($this->data->$key);
		}

		public function debug() {
			debug($this);
		}

		private function get_content() {
			$login = login::load();
			$DB =_pdo::load('connect');

			switch($this->type) {
				case 'posts': {
					$post = template::load('posts');
					$comments = template::load('comments');
					$comments_section = template::load('comments_section');
					$license = template::load('creative_commons');

					$comments_section->title(
						$this->data->title
					)->home(
						URL
					)->comments(
						null
					);

					$results = $DB->prepare("
						SELECT
							`comment`,
							`author`,
							`author_url`,
							`time`
						FROM `comments`
						WHERE `post` = :post
					")->bind([
						'post' => $this->data->url
					])->execute()->get_results();

					if(is_array($results)) {
						foreach($results as $comment) {
							$time = new simple_date($comment->time);
							$comments_section->comments .= $comments->comment(
								$comment->comment
							)->author(
								(strlen($comment->author_url)) ? "<a href=\"{$comment->author_url}\" target=\"_blank\">{$comment->author}</a>" : $comment->author
							)->time(
								$time->out('l, F jS Y h:i A')
							)->out();
						}
					}

					foreach(explode(',', $this->data->keywords) as $tag) {
						$post->tags .= '<a href="' . URL . '/tags/' . urlencode(trim($tag)) . '" rel="tag">' . trim($tag) . "</a>";
					}

					$time = new simple_date($this->data->created);

					$this->content = $post->title(
						$this->data->title
					)->content(
						$this->data->content
					)->home(
						URL
					)->comments(
						$comments_section->out()
					)->url(
						$this->data->url
					)->license(
						$license->title(
							$this->data->title
						)->author(
							$this->data->author
						)->author_url(
							$this->data->author_url
						)->date(
							$time->out('m/d/Y')
						)->datetime(
							$time->out()
						)->out()
					)->out();

				} break;

				case 'tags': {
					$this->title = 'Tags';
					$this->description = "Tags search results for {$this->path[1]}";
					$this->keywords = "Keywords, tags, search, {$this->path[1]}";
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

		private function error_page(
			$code = 404,
			$title_prefix = 'Woops! Not found',
			$dump = true
		) {
			http_response_code($code);
			$this->status = $code;
			$this->description = 'No results for ' . $this->url;
			$this->keywords = '';
			$this->title = $title_prefix .  ' (' . $code . ')';

			$template = template::load('error_page');
			$template->home = URL;
			$template->message = "Nothing found for <wbr /><var>{$this->url}</var>";
			$template->link = $this->url;
			if($dump) {
				$template->dump = print_r($this->parsed, true);
			}
			else {
				$template->dump = null;
			}
			$this->content = $template->out();
		}
	}
?>
