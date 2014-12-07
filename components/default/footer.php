<footer>
	<?php if($DB->connected):?>
	<?php foreach($DB->fetch_array("SELECT `url`, `icon`, `alt` FROM `footer_links` ORDER BY `order` ASC") as $link):?>
	<a href="<?=$link->url?>" target="_blank" class="logo" title="<?=$link->alt?>">
		<?php include(BASE . "/images/{$link->icon}")?>
	</a>
	<?php endforeach?>
	<span title="Recent Commits" class="logo" data-show-modal="#recent_commits_dialog"><?php include(BASE . '/images/logos/git.svg')?></span>
	<span title="Email Me" class="logo" data-show-modal="#email_admin_dialog"><?php include(BASE . '/images/icons/envelope.svg')?></span>
	<span title="contact Info" class="logo" data-show-modal="#contactDialog"><?php include(BASE . '/images/icons/people.svg')?></span>
	<?php endif?>
	<span title="Show README" class="logo" data-show-modal="#README"><?php include(BASE . '/images/icons/info.svg')?></span>
	<span title="Copyleft" class="logo" data-show-modal="#copyleftDialog"><?php include(BASE . '/images/logos/copyleft.svg')?></span>
	<?php load('contact', 'copyleft', 'forms/email_admin', 'recent_commits')?>
</footer>
