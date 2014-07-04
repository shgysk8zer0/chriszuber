<?php
	require_once(BASE . '/custom.php');
	$posts = get_recent_posts();
?>
<aside class="sidebar" rel="sidebar">
	<div class="recent posts">
		<h3>Recent Posts</h3>
		<?php foreach($posts as $post):?>
		<a href="<?=URL?>/posts/<?=$post->url?>">
			<h4><?=$post->title?></h4>
			<p><?=$post->description?></p>
		</a><br/>
		<?php endforeach?>
	</div>
	<div class="recent tags">
		<h3>Tags</h3>
		<?php foreach(get_all_tags() as $tag):?>
		<a href="<?=URL?>/tags/<?=urlencode($tag)?>"data-icon="," rel=><?=$tag?></a><br/>
		<?php endforeach?>
	</div>
</aside>