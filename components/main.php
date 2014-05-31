<?php
	$connect = ini::load('connect');
	if(isset($_SERVER['REDIRECT_URL']) and isset($_SERVER['REDIRECT_STATUS']) and $_SERVER['REDIRECT_STATUS'] === '200') {
		$path = explode('/', substr(preg_replace('/^' . preg_quote( '/' . $connect->site, '/') . '/', null, $_SERVER['REDIRECT_URL']), 1));
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
	<main role="main" itemprop="mainContentofPage" itemscope itemtype="http://schema.org/Blog">
		<article>
			<header>
				<h1 itemprop="headline"><?=$post->title?></h1>
				<nav>
					<?php foreach(explode(',', $post->keywords) as $tag):?>
					<a href="tags/<?=trim(strtolower($tag))?>"><?=trim(caps($tag))?></a>
					<?php endforeach?>
				</nav>
			</header>
			<section itemprop="text"><?=$post->content?></section>
			<footer>
				<a rel="license" href="http://creativecommons.org/licenses/by-sa/4.0/"><img alt="Creative Commons License" src="images/logos/CreativeCommons.svgz" /></a><br /><span xmlns:dct="http://purl.org/dc/terms/" property="dct:title"><?=$post->title?></span> by <a xmlns:cc="http://creativecommons.org/ns#" href="<?=$post->author_url?>?rel=author" property="cc:attributionName" rel="cc:attributionURL author" itemprop="author"><?=$post->author?></a> is licensed under a <a rel="license" href="http://creativecommons.org/licenses/by-sa/4.0/">Creative Commons Attribution-ShareAlike 4.0 International License</a>.<time datetime="<?=$time->out()?>" itemprop="datePublished"><?=$time->out('m/d/Y')?></time>
			</footer>
		</article>
	</main>
