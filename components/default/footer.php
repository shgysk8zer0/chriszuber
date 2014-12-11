<footer>
	<?php if($DB->connected): foreach($DB->fetch_array("SELECT `url`, `icon`, `alt` FROM `footer_links` ORDER BY `order` ASC") as $link):?>
	<a href="<?=$link->url?>" target="_blank" class="logo" title="<?=$link->alt?>">
		<svg><use xlink:href="images/icons/combined.svg#<?=filename($link->icon);?>" /></svg>
	</a>
<?php endforeach;  endif;?>
	<span title="Recent Commits" class="logo" data-request="action=recent_commits">
		<svg><use xlink:href="images/icons/combined.svg#git" /></svg>
	</span>
	<span title="GitHub Issues" class="logo" data-request="action=github_issues">
		<svg><use xlink:href="images/icons/combined.svg#issues-open" /></svg>
	</span>
	<span title="Email Me" class="logo" data-show-modal="#email_admin_dialog">
		<svg><use xlink:href="images/icons/combined.svg#envelope" /></svg>
	</span>
	<span title="Contact Info" class="logo" data-show-modal="#contactDialog">
		<svg><use xlink:href="images/icons/combined.svg#people" /></svg>
	</span>
	<span title="Show README" class="logo" data-request="action=README">
		<svg><use xlink:href="images/icons/combined.svg#info" /></svg>
	</span>
	<span title="Copyleft" class="logo" data-show-modal="#copyleftDialog">
		<svg><use xlink:href="images/icons/combined.svg#copyleft" /></svg>
	</span>
	<?php load('contact', 'copyleft', 'forms/email_admin')?>
</footer>
