<?php
	$connect = ini::load('connect');
	$login = login::load();
	if(isset($_SERVER['REDIRECT_URL']) and isset($_SERVER['REDIRECT_STATUS']) and $_SERVER['REDIRECT_STATUS'] === '200') {
		$path = explode('/', urldecode(preg_replace('/^(' . preg_quote(URL, '/')  .')?(' .preg_quote($connect->site, '/') . ')?(\/)?/', null, strtolower($_SERVER['REDIRECT_URL']))));
		if($path[0] === 'posts' and isset($path[1])){
			$post = $DB->prepare('
				SELECT *
				FROM `posts`
				WHERE `url` = :title
				ORDER BY `created`
				LIMIT 1
			')->bind([
				'title' => strtolower($path[1])
			])->execute()->get_results(0);

			$time = new simple_date($post->created);
			$keywords = explode(',', $post->keywords);
			$tags = [];
			foreach(explode(',', $post->keywords) as $tag) $tags[] = '<a href="' . URL . '/tags/' . strtolower(urlencode(trim($tag))) . '">' . trim(caps($tag)) . "</a>";

			$template = template::load('posts');
			$output = $template->set([
				'title' => $post->title,
				'tags' => join(PHP_EOL, $tags),
				'content' => $post->content,
				'author' => $post->author,
				'author_url' => $post->author_url,
				'date' => $time->out('m/d/Y'),
				'datetime' => $time->out()
			])->out();
		}
		elseif($path[0] === 'tags' and isset($path[1])){
			$output = '<div class="tags">';
			$posts = $DB->prepare("
				SELECT `title`, `description`, `author`, `author_url`, `url`, `created`
				FROM `posts`
				WHERE `keywords` LIKE :tag
				LIMIT 20
			")->bind([
				'tag' => "%{$path[1]}%"
			])->execute()->get_results();

			$template = template::load('tags');

			foreach($posts as $post) {
				$datetime = new simple_date($post->created);
				$output .= $template->set([
					'title' => $post->title,
					'description' => $post->description,
					'author' => $post->author,
					'author_url' => $post->author_url,
					'url' => $post->url,
					'date' => $datetime->out('D M jS, Y \a\t h:iA')
				])->out();
			}
			$output .= '</div>';
		}
	}
	else {
		$post = $DB->fetch_array("
			SELECT * FROM
			`posts`
			WHERE `url` = ''
			ORDER BY `created`
			LIMIT 1
			", 0
		);

			$time = new simple_date($post->created);
			$keywords = explode(',', $post->keywords);
			$tags = [];
			foreach(explode(',', $post->keywords) as $tag) $tags[] = '<a href="' . URL . '/tags/' . trim(strtolower(preg_replace('/\s/', '-', trim($tag)))) . '">' . trim(caps($tag)) . "</a>";

			$template = template::load('posts');
			$output = $template->set([
				'title' => $post->title,
				'tags' => join(PHP_EOL, $tags),
				'content' => $post->content,
				'author' => $post->author,
				'author_url' => $post->author_url,
				'date' => $time->out('m/d/Y'),
				'datetime' => $time->out()
			])->out();
	}
?>
<main role="main" itemprop="mainContentofPage" itemscope itemtype="http://schema.org/Blog" <?=($login->logged_in) ? ' data-menu="admin"' : ''?>>
	<?= $output?>
</main>
