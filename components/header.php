<?php
	$session = session::load();
	$storage = storage::load();
	$posts = $DB->fetch_array("SELECT `title`, `url` FROM `posts` ORDER BY `created` LIMIT 10");
?>
	<header>
		<h1><?=$storage->site_info->title?></h1>
		<nav>
			<menu type="list">
				<?php foreach($posts as $post):?>
				<li><a href="posts/<?=$post->url?>"><?=$post->title?></a></li>
				<?php endforeach?>
			</menu>
		</nav>
	</header>
