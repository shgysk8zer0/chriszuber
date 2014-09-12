<?php
	/**
	 * autoload function allowing for optional namespaces
	 *
	 * @param  string    $cname [Class Name]
	 * @return boolean
	 */

	function auto_load($cname) {
		/*
			Store $paths & $exts as static vars so we only have to get
			them once
		 */
		static $exts = null, $paths = null;
		if(is_null($exts)) {
			$exts = explode(',', str_replace(' ', null, spl_autoload_extensions()));
			$paths = explode(PATH_SEPARATOR, str_replace(' ', null, get_include_path()));
		}

		/*
			Convert namespaces to paths
		 */
		$cname = str_replace('\\', DIRECTORY_SEPARATOR, trim($cname, '\\'));

		/*
			Loop through $paths & $exts until file is found.
			Include & return true when found.
		 */
		foreach($paths as $path) {
			foreach($exts as $ext) {
				if(@file_exists($path. DIRECTORY_SEPARATOR . $cname . $ext)) {
					include($path . DIRECTORY_SEPARATOR . $cname . $ext);
					return true;
				}
			}
		}

		/*
			If file still not found after searching all paths & exts,
			return false because it doesn't exist
		 */
		return false;
	}

	function get_template($template) {
		return file_get_contents(BASE . "/components/templates/{$template}.tpl");
	}

	function update_sitemap() {
		$pdo = _pdo::load('connect');
		$url = preg_replace('/^https/', 'http', URL);
		$template = template::load('sitemap');
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
			$time = new simple_date($page->created);
			fputs($sitemap, $template->url( "{$url}/posts/{$page->url}")->mod($time->out('Y-m-d'))->priority('0.8')->out());
		}
		fputs($sitemap, '</urlset>');
		fclose($sitemap);
	}

	function update_rss($lim = 10) {
		$pdo = _pdo::load('connect');
		if($pdo->connected) {
			$url = preg_replace('/^https/', 'http', URL);
			$head = $pdo->name_value('head');
			$template = template::load('rss');
			$rss = fopen(BASE . '/feed.rss', 'w');
			$pages = $pdo->fetch_array("
				SELECT `title`, `url`, `description`, `created`
				FROM `posts`
				WHERE `url` != ''
				ORDER BY `created` DESC
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
				fputs($rss, $template->title($page->title)->url("{$url}/posts/{$page->url}")->description($page->description)->created(date('r', strtotime($page->created)))->out());
			}

			fputs($rss, '</channel>');
			fputs($rss, '</rss>');
			fclose($rss);
		}
	}

	function get_all_tags(){
		$pdo = _pdo::load();
		if($pdo->connected) {
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
		else {
			return [];
		}
	}

	function get_recent_posts($n = 5, $sel = ['title', 'url', 'description']) {
		$pdo = _pdo::load();

		if($pdo->connected) {
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
				ORDER BY `created` DESC
				LIMIT {$n}
			");
		}
		else {
			return [];
		}
	}

	function get_datalist($list) {
		$pdo = _pdo::load();
		switch(strtolower($list)) {
			case 'tags': {
				if(!$pdo->connected) return null;
				$options = get_all_tags();
			} break;

			case 'php_errors_files': {
				if(!$pdo->connected) return null;
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
