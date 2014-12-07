<?php
	/**
	 * Update sitemap.xml
	 *
	 * @param  string  $name  [Name of file output]
	 * @return void
	 */

	function update_sitemap($name = 'sitemap.xml') {
		$home = preg_replace('/^https/', 'http', URL);
		$sitemap = new \DOMDocument('1.0', 'UTF-8');
		$urlset = new \DOMElement(
			'urlset',
			null,
			'http://www.sitemaps.org/schemas/sitemap/0.9'
		);

		$sitemap->appendChild($urlset);

		array_map(function($post) use ($home, &$urlset){
			$url = new \DOMElement('url');
			$urlset->appendChild($url);
			$url->appendChild(new \DOMElement(
				'loc',
				"{$home}/posts/{$post->url}"
			));
			$url->appendChild(new \DOMElement(
				'lastmod',
				date('Y-m-d', strtotime($post->created))
			));
			$url->appendChild(new \DOMElement('priority', '0.8'));
		}, \core\PDO::load('connect')->fetch_array("
			SELECT `url`, `created`
			FROM `posts`
			WHERE `url` != ''
			ORDER BY `created` DESC
		"));

		$sitemap->save(__DIR__ . DIRECTORY_SEPARATOR . $name);
	}

	/**
	 * Updates feed.rss with the $lim most recent posts
	 *
	 * @param  integer $lim   [Max number to include]
	 * @param  string  $name  [Name of file output]
	 * @return void
	 */

	function update_rss($lim = 10, $name = 'feed.rss') {
		$lim = (int)$lim;
		$pdo = \core\PDO::load('connect');
		if($pdo->connected) {
			$url = preg_replace('/^https/', 'http', URL);
			$head = $pdo->name_value('head');

			$feed = new \DOMDocument('1.0', 'UTF-8');
			$rss = new \DOMElement('rss');
			$feed->appendChild($rss);
			$rss->setAttribute('version', '2.0');
			$channel = new \DOMElement('channel');
			$rss->appendChild($channel);
			$channel->appendChild(new \DOMElement(
				'title',
				htmlspecialchars($head->title, ENT_XML1, 'UTF-8')
			));
			$channel->appendChild(new \DOMElement('link', URL));
			$channel->appendChild(new \DOMElement('lastBuildDate', date('r')));
			$channel->appendChild(new \DOMElement('language', 'en-us'));
			$channel->appendChild(new \DOMElement(
				'description',
				htmlspecialchars($head->description, ENT_XML1, 'UTF-8')
			));

			array_map(function($post) use (&$rss, $url) {
				$item = new \DOMElement('item');
				$rss->appendChild($item);
				$item->appendChild(new \DOMElement(
					'title',
					htmlspecialchars($post->title, ENT_XML1, 'UTF-8')
				));
				$item->appendChild(new \DOMElement(
					'link',
					"{$url}/posts/{$post->url}"
				));
				$item->appendChild(new \DOMElement(
					'description',
					htmlspecialchars($post->description, ENT_XML1, 'UTF-8')
				));
				$item->appendChild(new \DOMElement(
					'pubDate',
					date('r', strtotime($post->created))
				));
				$item->appendChild(new \DOMElement(
					'guid',
					"{$url}/posts/{$post->url}"
				));
			}, $pdo->fetch_array("
				SELECT `title`, `url`, `description`, `created`
				FROM `posts`
				WHERE `url` != ''
				ORDER BY `created` DESC
				LIMIT {$lim}
			"));

			$feed->save(BASE . DIRECTORY_SEPARATOR . $name);
		}
	}

	/**
	 * Gets all keywords for all posts
	 *
	 * @param void
	 * @return array [Unique keywords for all posts]
	 */

	function get_all_tags(){
		$pdo = \core\PDO::load('connect');
		if($pdo->connected) {
			return array_unique(flatten(array_map(function($result) {
				return array_map(
					'trim',
					explode(',', $result->keywords)
				);
			}, $pdo->fetch_array("
				SELECT `keywords`
				FROM `posts`
			"))));
		}
		else {
			return [];
		}
	}


	/**
	 * Get $selectors for the $limit most recent posts
	 *
	 * @param  integer $limit     [Max number for return]
	 * @param  array  $selectors  [Select these columns]
	 * @return array
	 */

	function get_recent_posts($limit = 5, array $selectors = null) {
		$pdo =\core\PDO::load('connect');

		if($pdo->connected) {
			if(!is_array($selectors)) {
				$selectors = [
					'title',
					'url',
					'description'
				];
			}

			array_walk($selectors, [$pdo, 'escape']);
			$selectors = '`' . join('`, `', $selectors) . '`';

			return $pdo->fetch_array("
				SELECT {$selectors}
				FROM `posts`
				WHERE `url` != ''
				ORDER BY `created`
				DESC
				LIMIT {$limit}
			");
		}
		else {
			return [];
		}
	}

	/**
	 * Builds a <datalist> for the request, each result being a <option>
	 *
	 * @param  string $list [Requested datalist]
	 * @return string       [Results as a <datalist>]
	 */

	function get_datalist($list) {
		$pdo = \core\PDO::load('connect');
		$datalist = "<datalist id=\"{$list}\">";

		if($pdo->connected) {
			switch(strtolower($list)) {
				case 'tags': {
					$options = get_all_tags();
				} break;

				case 'php_errors_files': {
					$options = array_map(function($option) {
						return preg_replace(
							'/^' . preg_quote(BASE . DIRECTORY_SEPARATOR, '/') . '/',
							null,
							$option->file
						);
					}, $pdo->fetch_array("
						SELECT DISTINCT(`file`)
						FROM `PHP_errors`
					"));
				} break;
			}
		}

		if(isset($options)) {
			foreach($options as $option) {
				$datalist .= "<option value=\"{$option}\"></option>";
			}
		}

		$datalist .= "</datalist>";
		return $datalist;
	}
?>
