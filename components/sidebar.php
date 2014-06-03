<?php
	require_once(BASE . '/custom.php');
	$posts = get_recent_posts();
	//$tags = get_all_tags();
?>
<aside class="sidebar">
	<div class="recent posts">
		<h3>Recent Posts</h3>
		<?php foreach($posts as $post):?>
		<a href="<?=URL?>/posts/<?=$post->url?>">
			<?=$post->title?>
			<p><?=$post->description?></p>
		</a>
		<?php endforeach?>
		<?php load('forms/tag_search')?>
	</div>
	<div class="recent tags">
		<h3>Tags</h3>
		<?php foreach(get_all_tags() as $tag):?>
		<a href="<?=URL?>/tags/<?=urlencode($tag)?>"><?=$tag?></a>
		<?php endforeach?>
	</div>
</aside>