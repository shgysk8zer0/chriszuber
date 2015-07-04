<?php
	$storage = \shgysk8zer0\Core\storage::load();
?>
	<header role="banner" id="header">
		<h1 class="center">
			<a href="<?=URL?>" rel="bookmark">
				<?=$storage->site_info->title?>
			</a>
		</h1>
		<nav role="navigation" class="flex row wrap">
			<?php foreach(get_recent_posts(5) as $post):?>
			<a href="<?=URL?>posts/<?=$post->url?>" class="center"><?=$post->title?></a>
			<?php endforeach?>
		</nav>
		<?php load(($DB->connected) ? 'forms/tag_search' : 'buttons/install')?>
	</header>
