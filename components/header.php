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
		<h1><a href="<?=URL?>"><?=$storage->site_info->title?></a></h1>
		<nav>
			<menu type="list">
				<?php foreach($posts as $post):?>
				<li><a href="<?=URL?>/posts/<?=$post->url?>"><?=$post->title?></a></li>
				<?php endforeach?>
			</menu>
		</nav>
	</header>
