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
			<menu type="list">
				<?php foreach($posts as $post):?>
				<li><a href="<?=URL?>/posts/<?=$post->url?>"><?=$post->title?></a></li>
				<?php endforeach?>
			</menu>
			<?php load('forms/tag_search')?>
		</nav>
	</header>
