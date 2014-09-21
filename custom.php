<?php
	/**
	 * Update sitemap.xml
	 *
	 * @param void
	 * @return void
	 */

	function update_sitemap() {
		$pdo = \core\_pdo::load('connect');
		$url = preg_replace('/^https/', 'http', URL);
		$template = \core\template::load('sitemap');
		$sitemap = fopen(BASE . '/sitemap.xml', 'w');
		$pages = $pdo->fetch_array("
			SELECT `url`, `created`
			FROM `posts`
			WHERE `url` != ''
			ORDER BY `created` DESC
		");
		fputs($sitemap, '<?xml version="1.0" encoding="UTF-8"?>');
		fputs($sitemap, '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">');
		foreach($pages as $page) {
			$time = new \core\simple_date($page->created);
			fputs(
				$sitemap,
				$template->url(
					"{$url}/posts/{$page->url}"
				)->mod(
					$time->out('Y-m-d')
				)->priority(
					'0.8'
				)->out()
			);
		}
		fputs($sitemap, '</urlset>');
		fclose($sitemap);
	}

	/**
	 * Updates feed.rss with the $lim most recent posts
	 *
	 * @param  integer $lim [Max number to include]
	 * @return void
	 */

	function update_rss($lim = 10) {
		$pdo = \core\_pdo::load('connect');
		if($pdo->connected) {
			$url = preg_replace('/^https/', 'http', URL);
			$head = $pdo->name_value('head');
			$template = \core\template::load('rss');
			$rss = fopen(BASE . '/feed.rss', 'w');
			$pages = $pdo->fetch_array("
				SELECT
					`title`,
					`url`,
					`description`,
					`created`
				FROM `posts`
				WHERE `url` != ''
				ORDER BY `created`
				DESC
			");

			fputs($rss, '<?xml version="1.0" encoding="UTF-8" ?>' . PHP_EOL);
			fputs($rss, '<rss version="2.0">' . PHP_EOL);
			fputs($rss, '<channel>' . PHP_EOL);
			fputs($rss, "<title>{$head->title}</title>" . PHP_EOL);
			fputs($rss, "<link>{$url}</link>" . PHP_EOL);
			fputs($rss, "<lastBuildDate>" . date('r') ."</lastBuildDate>" . PHP_EOL);
			fputs($rss, "<language>en-US</language>" . PHP_EOL);
			fputs($rss, "<description>{$head->description}</description>" . PHP_EOL);

			foreach($pages as $page) {
				fputs(
					$rss,
					$template->title(
						$page->title
					)->url(
						"{$url}/posts/{$page->url}"
					)->description(
						$page->description
					)->created(
						date('r', strtotime($page->created))
					)->out());
			}

			fputs($rss, '</channel>');
			fputs($rss, '</rss>');
			fclose($rss);
		}
	}

	/**
	 * Gets all keywords for all posts
	 *
	 * @param void
	 * @return array [Unique keywords for all posts]
	 */

	function get_all_tags(){
		$pdo =\core\_pdo::load('connect');
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
		$pdo =\core\_pdo::load('connect');

		if($pdo->connected) {

			if(!is_array($selectors)) {
				$selectors = ['title', 'url', 'description'];
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
		$pdo =\core\_pdo::load('connect');

		$datalist = "<datalist id=\"{$list}\">";
		if($pdo->connected) {
			switch(strtolower($list)) {
				case 'tags': {
					$options = get_all_tags();
				} break;

				case 'php_errors_files': {
					$options = array_map(function($option) {
						return preg_replace(
							'/^' . preg_quote(BASE . '/', '/') . '/',
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
