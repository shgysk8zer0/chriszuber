<?php
	$posts = get_recent_posts(10);
	$template = template::load('recent_posts');
?>
<aside class="sidebar" rel="sidebar">
	<div class="recent posts">
		<h3>Recent Posts</h3>
		<?php foreach($posts as $post) {
			echo $template->title($post->title)->description($post->description)->link(URL . "/posts/{$post->url}")->out();
		}?>
	</div>
	<div class="recent tags">
		<h3>Tags</h3>
		<?php foreach(get_all_tags() as $tag):?>
		<div>
			<a href="<?=URL?>/tags/<?=urlencode($tag)?>" data-icon=","><?=$tag?></a>
		</div><br/>
		<?php endforeach?>
	</div><br />
</aside>