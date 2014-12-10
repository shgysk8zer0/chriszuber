<footer>
	<?php if($DB->connected): foreach($DB->fetch_array("SELECT `url`, `icon`, `alt` FROM `footer_links` ORDER BY `order` ASC") as $link):?>
	<a href="<?=$link->url?>" target="_blank" class="logo" title="<?=$link->alt?>">
		<?php include(BASE . "/images/{$link->icon}")?>
	</a>
<?php endforeach;  endif;?>
	<span title="Recent Commits" class="logo" data-request="action=recent_commits"><?php include(BASE . '/images/logos/git.svg')?></span>
	<span title="GitHub Issues" class="logo" data-request="action=github_issues"><?php include(BASE . '/images/icons/issues-open.svg')?></span>
	<span title="Email Me" class="logo" data-show-modal="#email_admin_dialog"><?php include(BASE . '/images/icons/envelope.svg')?></span>
	<span title="Contact Info" class="logo" data-show-modal="#contactDialog"><?php include(BASE . '/images/icons/people.svg')?></span>
	<span title="Show README" class="logo" data-request="action=README"><?php include(BASE . '/images/icons/info.svg')?></span>
	<span title="Copyleft" class="logo" data-show-modal="#copyleftDialog"><?php include(BASE . '/images/logos/copyleft.svg')?></span>
	<?php load('contact', 'copyleft', 'forms/email_admin')?>
</footer>
