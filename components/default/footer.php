<footer>
	<?php if($DB->connected):
		foreach($DB->fetch_array("SELECT
			`url`,
			`icon`,
			`alt`
		FROM `footer_links`
		ORDER BY `order` ASC;
	") as $link):?>
	<a href="<?=$link->url?>" target="_blank" class="logo currentColor" title="<?=$link->alt?>">
		<?=SVG_use(filename($link->icon));?>
	</a>
	<?php
		endforeach;
		endif;

		foreach([
			'git-commit' => [
				'title' => 'Recent Commits',
				'class' => 'logo currentColor',
				'data-request' => "action=recent_commits"
			],
			'issue-opened' => [
				'title' => 'GitHub Issues',
				'class' => 'logo currentColor',
				'data-request' => 'action=github_issues'
			],
			'mail' => [
				'title' => 'Email Me',
				'class' => 'logo currentColor',
				'data-show-modal' => '#email_admin_dialog'
			],
			'people' => [
				'title' => 'Contact Info',
				'class' => 'logo currentColor',
				'data-show-modal' => '#contactDialog'
			],
			'question' => [
				'title' => 'Show README',
				'class' => 'logo currentColor',
				'data-request' => 'action=README'
			],
			'copyleft' => [
				'title' => 'Copyleft',
				'class' => 'logo currentColor',
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
