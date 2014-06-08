<?php
	$session = session::load();
	$storage = storage::load();
	$posts = $DB->fetch_array("
		SELECT `title`, `url`
		FROM `posts`
		WHERE `url` != ''
		ORDER BY `created`
		LIMIT 10
	");
?>
	<header>
		<h1><a href="<?=URL?>" data-cache="home"><?=$storage->site_info->title?></a></h1>
		<nav>
			<?php foreach($posts as $post):?>
			<a href="<?=URL?>/posts/<?=$post->url?>" itemprop="keywords"><?=$post->title?></a>
			<?php endforeach?>
			<?php load('forms/tag_search')?>
		</nav>
	</header>
