<?php
	$storage = \shgysk8zer0\core\storage::load();
?>
	<header role="banner">
		<h1>
			<a href="<?=URL?>" rel="bookmark">
				<?=$storage->site_info->title?>
			</a>
		</h1>
		<nav role="navigation">
			<?php foreach(get_recent_posts(5) as $post):?>
			<a href="<?=URL?>/posts/<?=$post->url?>"><?=$post->title?></a>
			<?php endforeach?>
		</nav>
		<?php load(($DB->connected) ? 'forms/tag_search' : 'buttons/install')?>
	</header>
