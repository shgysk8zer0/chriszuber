<footer>
	<?php if($DB->connected): foreach($DB->fetch_array("SELECT `url`, `icon`, `alt` FROM `footer_links` ORDER BY `order` ASC") as $link):?>
	<a href="<?=$link->url?>" target="_blank" class="logo" title="<?=$link->alt?>">
		<?=SVG_use(filename($link->icon));?>
	</a>
<?php endforeach;  endif;?>
	<span title="Recent Commits" class="logo" data-request="action=recent_commits">
		<?=SVG_use('git');?>
	</span>
	<span title="GitHub Issues" class="logo" data-request="action=github_issues">
		<?=SVG_use('issues-open');?>
	</span>
	<span title="Email Me" class="logo" data-show-modal="#email_admin_dialog">
		<?=SVG_use('envelope');?>
	</span>
	<span title="Contact Info" class="logo" data-show-modal="#contactDialog">
		<?=SVG_use('people');?>
	</span>
	<span title="Show README" class="logo" data-request="action=README">
		<?=SVG_use('info');?>
	</span>
	<span title="Copyleft" class="logo" data-show-modal="#copyleftDialog">
		<?=SVG_use('copyleft');?>
	</span>
	<?php load('contact', 'copyleft', 'forms/email_admin')?>
</footer>
