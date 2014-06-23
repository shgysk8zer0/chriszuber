<?php
	$session = session::load();
	$storage = storage::load();
	$posts = $DB->fetch_array("
		SELECT `title`, `url`
		FROM `posts`
		WHERE `url` != ''
		ORDER BY `created` DESC
		LIMIT 10
	");
?>
	<header>
		<h1><a href="<?=URL?>" rel="bookmark" data-cache="home"><?=$storage->site_info->title?></a></h1>
		<nav itemprop="keywords" role="navigation">
			<?php foreach($posts as $post):?>
			<a href="<?=URL?>/posts/<?=$post->url?>"><?=$post->title?></a>
			<?php endforeach?>
			<?php load('forms/tag_search')?>
		</nav>
	</header>
