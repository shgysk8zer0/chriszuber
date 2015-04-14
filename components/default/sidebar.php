<?php
	$template = \shgysk8zer0\Core\Template::load('recent_posts');
?>
<aside class="sidebar" rel="sidebar">
	<div class="recent posts">
		<h3>Recent Posts</h3>
		<?=array_reduce(
			get_recent_posts(15),
			function($html, \stdClass $post) use ($template, $URL)
			{
				return $html .= $template->title($post->title)
					->description($post->description)
					->link("{$URL}posts/{$post->url}");
			}
		)?>
	</div>
	<div class="recent tags">
		<h3>Tags</h3>
		<?php
			$dom = new \DOMDocument('1.0', 'UTF-8');
			$ul = array_reduce(
				get_all_tags(),
				function(\DOMElement $list, $item) use ($URL)
				{
					$li = $list->appendChild(new \DOMElement('li'));
					$a = $li->appendChild(new \DOMElement('a', $item));
					$a->setAttribute('href', $URL . 'tags/' . urlencode($item));
					$a->setAttribute('data-icon', ',');
					return $list;
				},
				$dom->appendChild(new \DOMElement('ul'))
			);
			echo $dom->saveHTML($ul);
		?>
	</div>
</aside>
