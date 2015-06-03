<?php
	$template = \shgysk8zer0\Core\Template::load('recent_posts');
?>
<aside class="sidebar" rel="sidebar">
	<div class="recent posts">
		<h3 class="sticky">Recent Posts</h3>
		<?= array_reduce(
			get_recent_posts(15),
			function($html = '', \stdClass $post) use ($template, $URL)
			{
				return $html .= $template->title($post->title)
					->description($post->description)
					->link("{$URL}posts/{$post->url}");
			}
		)?>
	</div>
	<div class="recent tags">
		<h3 class="sticky">Tags</h3>
		<?= array_reduce(
				get_all_tags(),
				'recent_tags_list',
				$doc->appendChild($doc->createElement('ul'))
			);
		?>
	</div>
</aside>
<?php ob_flush(); flush();?>
