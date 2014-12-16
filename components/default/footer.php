<footer>
	<?php if($DB->connected):
		foreach($DB->fetch_array("SELECT
			`url`,
			`icon`,
			`alt`
		FROM `footer_links`
		ORDER BY `order` ASC;
	") as $link):?>
	<a href="<?=$link->url?>" target="_blank" class="logo" title="<?=$link->alt?>">
		<?=SVG_use(filename($link->icon));?>
	</a>
	<?php
		endforeach;
		endif;

		foreach([
			'git' => [
				'title' => 'Recent Commits',
				'class' => 'logo',
				'data-request' => "action=recent_commits"
			],
			'issues-open' => [
				'title' => 'GitHub Issues',
				'class' => 'logo',
				'data-request' => 'action=github_issues'
			],
			'envelope' => [
				'title' => 'Email Me',
				'class' => 'logo',
				'data-show-modal' => '#email_admin_dialog'
			],
			'people' => [
				'title' => 'Contact Info',
				'class' => 'logo',
				'data-show-modal' => '#contactDialog'
			],
			'info' => [
				'title' => 'Show README',
				'class' => 'logo',
				'data-request' => 'action=README'
			],
			'copyleft' => [
				'title' => 'Copyleft',
				'class' => 'logo',
				'data-show-modal' => '#copyleftDialog'
			]
		] as $icon => $attributes) :{?>
			<span <?=join(' ', array_map(function($key, $value) {
				return "{$key}=\"{$value}\"";
			}, array_keys($attributes), array_values($attributes)))?>>
				<?=SVG_use($icon);?>
			</span>
	<?php }
		endforeach;
		load(
			'contact',
			'copyleft',
			'forms/email_admin'
		);
	?>
</footer>
