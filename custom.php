<?php
/**
 * Create a script tag as a string
 *
 * @param  string $src   URL src for script
 * @param  bool   $async Whether or not to set the async attribute
 * @param  bool   $defer Whether or not to set the async attribute
 * @param  string $type  Type Attribute (because Firefox can handle additional "version")
 *
 * @return string        "<script ...></script>"
 */
function mk_script_tag($src, $async = true, $defer = false, $type = 'application/javascript')
{
	$script = "<script type=\"{$type}\" src=\"{$src}\"";
	if ($async) {
		$script .= ' async=""';
	}
	if ($defer) {
		$script .= ' defer=""';
	}
	$script .= '></script>';
	return $script;
}

/**
 * Get an array of scipts to use, compatible with `mk_script_tag`
 *
 * @param void
 * @return array [[src, async, defer, type], ...]
 */
function get_dev_scripts()
{
	$type = (BROWSER === 'Firefox') ? 'application/javascript;version=1.8' : 'application/javascript';
	return array(
		array('scripts/std-js/deprefixer.js', true, false, $type),
		array('scripts/std-js/prototypes.js', true, false, $type),
		array('scripts/std-js/support_test.js', true, false, $type),
		array('scripts/std-js/poly_modern.js', true, false, $type),
		array('scripts/std-js/functions.js', true, false, $type),
		array('scripts/std-js/zq.js', true, false, $type),
		array('scripts/std-js/popstate.js', true, false, $type),
		array('scripts/std-js/json_response.js', true, false, $type),
		array('scripts/std-js/wysiwyg.js', true, false, $type),
		array('scripts/std-js/kbd_shortcuts.js', true, false, $type),
		array('scripts/custom.js', true, false, $type)
	);
}

/**
 * Concatenate JavaScript files
 *
 * @param  array  $scripts Scripts to concatenate from
 * @param  string $output  File to save concatenated scripts to
 *
 * @return void
 */
function concatenate_scripts(array $scripts, $output = 'scripts/combined.js')
{
	$handle = fopen($output, 'c');
	// Only proceed if we can obtain an exclusive lock
	if ($obtained = flock($handle, LOCK_EX)) {
		// Truncate the script to not just append to it
		ftruncate($handle, 0);
		// Iterate through the scripts, appending to the now empty file
		foreach ($scripts as $file) {
			fwrite($handle, file_get_contents($file));
		}
		// Release the lock
		flock($handle, LOCK_UN);
	}
	// Close the handle
	fclose($handle);
	return $obtained;
}

/**
 * Update sitemap.xml
 *
 * @param  string  $name  Name of file output
 * @return void
 */
function update_sitemap($name = 'sitemap.xml')
{
	$home = \shgysk8zer0\Core\URL::load(URL);
	$sitemap = new \shgysk8zer0\DOM\XML(
		'urlset',
		'http://www.sitemaps.org/schemas/sitemap/0.9',
		'1.0',
		'UTF-8'
	);

	$posts = \shgysk8zer0\Core\PDO::load()->fetchArray(
		'SELECT `url`, `created`
		FROM `posts`
		WHERE `url` != ""
		ORDER BY `created` DESC;'
	);

	$posts = new \shgysk8zer0\Core\ArrayObject($posts);
	$posts->reduce(
		function(\DOMElement $urlset, \stdClass $post) use ($home) {
			$url = $urlset->append('url');
			$url->append('loc', "{$home}posts/{$post->url}");
			$url->append('lastmod', date(DATE_RSS, strtotime($post->created)));
			$url->append('priority', 0.8);
			return $urlset;
		},
		$sitemap->documentElement
	);
	$sitemap->save(__DIR__ . DIRECTORY_SEPARATOR . $name);
}

/**
 * Updates feed.rss with the $lim most recent posts
 *
 * @param  int     $lim   Max number to include
 * @param  string  $name  Name of file output
 * @return void
 */
function update_rss($lim = 10, $name = 'feed.rss')
{
	if (! is_int($lim)) {
		$lim = 10;
	}
	$pdo = \shgysk8zer0\Core\PDO::load('connect');
	if($pdo->connected) {
		$url  = \shgysk8zer0\Core\URL::load(URL);
		$rss = new \shgysk8zer0\DOM\XML('rss', null, '1.0', 'UTF-8');
		$head = $pdo->nameValue('head');
		$posts = $pdo->fetchArray(
			"SELECT `title`, `url`, `description`, `created`
			FROM `posts`
			WHERE `url` != ''
			ORDER BY `created` DESC
			LIMIT {$lim};"
		);
		$posts = new \shgysk8zer0\Core\ArrayObject($posts);

		$rss->documentElement->version = '2.0';
		$channel = $rss->documentElement->append('channel');
		$site = array(
			'title' => htmlspecialchars($head->title, ENT_XML1, $rss->encoding),
			'description' => htmlspecialchars($head->description, ENT_XML1, $rss->encoding),
			'link' => URL,
			'lastBuildDate' => date(DATE_RSS),
			'laneguage' => 'en-us'
		);
		array_map([$channel, 'append'], array_keys($site), array_values($site));

		$posts->reduce(
			function(\DOMElement $feed, \stdClass $post) use ($url) {
				$item = $feed->append('item');
				$item->append('title', htmlspecialchars($post->title, ENT_XML1, $feed->ownerDocument->encoding));
				$item->append('link', "{$url}posts/{$post->url}");
				$item->append('description', htmlspecialchars($post->description, ENT_XML1, $feed->ownerDocument->encoding));
				$item->append('pubDate', date(DATE_RSS, strtotime($post->created)));
				$item->append('guid', "{$url}posts/{$post->url}");
				return $feed;
			},
			$rss->documentElement
		);

		$rss->save(BASE . DIRECTORY_SEPARATOR . $name);
	}
}

/**
 * Gets all keywords for recent posts
 *
 * @param  int $limit  Max number of recent posts to get tags from
 * @return array       Unique keywords for the posts
 */
function get_all_tags($limit = 5)
{
	if (! is_int($limit)) {
		throw new \InvalidArgumentException(
			sprintf('%s expects $limit to be an integer, %s given', __FUNCTION__, gettype($limit))
		);
	}
	$pdo = \shgysk8zer0\Core\PDO::load('connect.json');
	if($pdo->connected) {
		return array_unique(flatten(array_map(function(\stdClass $result)
		{
			return array_map(
				'trim',
				explode(',', $result->keywords)
			);
		}, $pdo->fetchArray(
			"SELECT DISTINCT(`keywords`)
			FROM `posts`
			ORDER BY `created` DESC
			limit {$limit};"
		)
	)));
	} else {
		return [];
	}
}


/**
 * Get $selectors for the $limit most recent posts
 *
 * @param  int    $limit      Max number for return
 * @param  array  $selectors  Select these columns
 * @return array
 */
function get_recent_posts($limit = 5, array $selectors = array())
{
	$pdo = \shgysk8zer0\Core\PDO::load('connect.json');

	if($pdo->connected) {
		if (! is_int($limit)) {
			$limit = 5;
		}
		if(empty($selectors)) {
			$selectors = [
				'title',
				'url',
				'description'
			];
		}

		array_walk($selectors, [$pdo, 'escape']);
		$selectors = '`' . join('`, `', $selectors) . '`';

		return $pdo->fetchArray(
			"SELECT {$selectors}
			FROM `posts`
			WHERE `url` != ''
			ORDER BY `created`
			DESC
			LIMIT {$limit};"
		);
	} else {
		return [];
	}
}

/**
 * Builds a <datalist> for the request, each result being a <option>
 *
 * @param  string $list Requested datalist
 * @return string       Results as a <datalist>
 * @uses \DOMDocument
 * @uses \DOMElement
 */
function get_datalist($list)
{
	$pdo = \shgysk8zer0\Core\PDO::load('connect.json');
	if (! $pdo->connected) {
		return;
	}

	switch(strtolower($list)) {
		case 'tags':
			$options = get_all_tags();
			break;

		case 'php_errors_files':
			$options = array_map(
				function(\stdClass $option)
				{
					return preg_replace(
						'/^' . preg_quote(BASE . DIRECTORY_SEPARATOR, '/') . '/',
						null,
						$option->file
					);
				},
				$pdo->fetchArray("SELECT DISTINCT(`file`) FROM `PHP_errors`;")
			);
			break;

		default:
			return [];
	}

	$datalist = array_reduce(
		$options,
		function(\DOMElement $list, $item)
		{
			$list->option = ['@value' => $item];
			return $list;
		},
		new \shgysk8zer0\Core\HTML_El('datalist', null, null, true)
	);
	$datalist->{'@id'} = "$list";
	return "{$datalist}";
}

/**
* Adds <li>s & <a>s to a list element, specifically for recent tags
*
* @param  shgysk8zer0\Core_API\Interfaces\MagicDOM  $list <ol> or <ul>
* @param  string                                    $item The tag
* @return shgysk8zer0\Core_API\Interfaces\MagicDOM  List with tag appended
*/
function recent_tags_list(\shgysk8zer0\Core_API\Interfaces\MagicDOM $list, $item = '')
{
	return $list->li(array(
		'a' => array(
			$item,
			'@href' => sprintf('%stags/%s', URL, $item),
			'@data-icon' => ','
		)
	));
}
