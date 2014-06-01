<?php
	$connect = ini::load('connect');
	$login = login::load();
	if(isset($_SERVER['REDIRECT_URL']) and isset($_SERVER['REDIRECT_STATUS']) and $_SERVER['REDIRECT_STATUS'] === '200') {
		$path = explode('/', substr(preg_replace('/^' . preg_quote( '/' . $connect->site, '/') . '/', null, urldecode($_SERVER['REDIRECT_URL'])), 1));
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
		}
	}
	else $post = $DB->fetch_array("SELECT * FROM `posts` ORDER BY `created` LIMIT 1", 0);
	$time = new simple_date($post->created);
	$keywords = explode(',', $post->keywords);
?>
<main role="main" itemprop="mainContentofPage" itemscope itemtype="http://schema.org/Blog" <?=($login->logged_in) ? ' data-menu="admin"' : ''?>>
	<?php
		$tags = [];
		foreach(explode(',', $post->keywords) as $tag) $tags[] = '<a href="' . URL . '/tags/' . trim(strtolower(preg_replace('/\s/', '-', trim($tag)))) . '">' . trim(caps($tag)) . "</a>";
		$template = template::load('blog');
		$template->set([
			'title' => $post->title,
			'tags' => join(PHP_EOL, $tags),
			'content' => $post->content,
			'author' => $post->author,
			'author_url' => $post->author_url,
			'date' => $time->out('m/d/Y'),
			'datetime' => $time->out()
		])->out();
	?>
</main>
