<?php

	function get_template($template) {
		return file_get_contents(BASE . "/components/templates/{$template}.tpl");
	}

	function update_sitemap() {
		$pdo = _pdo::load('connect');
		$template = template::load('sitemap');
		$sitemap = fopen(BASE . '/sitemap.xml', 'w');
		$pages = $pdo->fetch_array("
			SELECT `url`, `created`
			FROM `posts`
		");
		fputs($sitemap, '<?xml version="1.0" encoding="UTF-8"?>');
		fputs($sitemap, '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">');
		foreach($pages as $page) {
			$time = new simple_date($page->created);
			fputs($sitemap, $template->url(URL . "/{$page->url}")->mod($time->out('Y-m-d'))->priority('0.8')->out());
		}
		fputs($sitemap, '</urlset>');
		fclose($sitemap);
	}

	function update_rss($lim = 10) {
		$pdo = _pdo::load('connect');
		$head = $pdo->name_value('head');
		$template = template::load('rss');
		$rss = fopen(BASE . '/feed.rss', 'w');
		$pages = $pdo->fetch_array("
			SELECT `title`, `url`, `description`, `created`
			FROM `posts`
		");

		fputs($rss, '<?xml version="1.0" encoding="UTF-8" ?>' . PHP_EOL);
		fputs($rss, '<rss version="2.0">' . PHP_EOL);
		fputs($rss, '<channel>' . PHP_EOL);
		fputs($rss, "<title>{$head->title}</title>" . PHP_EOL);
		fputs($rss, "<link>" . URL . "</link>" . PHP_EOL);
		fputs($rss, "<lastBuildDate>" . date('r') ."</lastBuildDate>" . PHP_EOL);
		fputs($rss, "<language>en-US</language>" . PHP_EOL);
		fputs($rss, "<description>{$head->description}</description>" . PHP_EOL);

		foreach($pages as $page) {
			fputs($rss, $template->title($page->title)->url(URL . "/{$page->url}")->description($page->description)->created($page->created)->out());
		}

		fputs($rss, '</rss>');
		fputs($rss, '</channel>');
		fclose($rss);

	}

	function get_all_tags(){
		$pdo = _pdo::load();
		$keywords = flatten($pdo->fetch_array("
			SELECT `keywords` FROM `posts`
		"));
		$tags = [];
		foreach($keywords as $keyword) {
			foreach(explode(',', $keyword) as $tag) {
				$tags[] = trim($tag);
			}
		};
		return array_unique($tags);
	}

	function get_recent_posts($n = 5, $sel = ['title', 'url', 'description']) {
		$pdo = _pdo::load();
		if(is_string($sel) and $sel !== '*') {
			$sel = explode(',', $sel);
		}
		if(is_array($sel)) {
			foreach($sel as &$col) {
				$col = "`{$pdo->escape($col)}`";
			}
			$sel = join(', ', $sel);
		}
		return $pdo->fetch_array("
			SELECT `title`, `url`, `description`
			FROM `posts`
			WHERE `url` != ''
			ORDER BY `created`
			LIMIT {$n}
		");
	}

	function get_datalist($list) {
		$pdo = _pdo::load();
		switch(strtolower($list)) {
			case 'tags': {
				$options = get_all_tags();
			} break;

			case 'php_errors_files': {
				$options = $pdo->fetch_array("
					SELECT DISTINCT(`file`)
					FROM `PHP_errors`
				");
				foreach($options as &$option) {
					//$datalist .= "<option>" . preg_replace('/^' . preg_quote(BASE . '/', '/') . '/', null, $option->file) . '</option>';
					$option = preg_replace('/^' . preg_quote(BASE . '/', '/') . '/', null, $option->file);
				}
			} break;
		}
		if(isset($options)) {
			$datalist = "<datalist id=\"{$list}\">";
			foreach($options as $option) {
				$datalist .= "<option value=\"{$option}\">{$option}</option>";
			}
			$datalist .= "</datalist>";
			return $datalist;
		}
	}
?>
