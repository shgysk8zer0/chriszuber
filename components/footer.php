<footer>
	<?php if($DB->connected):?>
	<?php foreach($DB->fetch_array("SELECT `url`, `icon`, `alt` FROM `footer_links` ORDER BY `order` ASC") as $link):?>
	<a href="<?=$link->url?>" target="_blank" class="logo" title="<?=$link->alt?>">
		<?php include(BASE . "/images/{$link->icon}")?>
	</a>
	<?php endforeach?>
	<span title="contact Info" class="logo" data-show-modal="#contactDialog"><?php include(BASE . '/images/icons/people.svg')?></span>
	<?php endif?>
	<span title="Show README" class="logo" data-show-modal="#README"><?php include(BASE . '/images/icons/info.svg')?></span>
	<span title="Copyleft" class="logo" data-show-modal="#copyleftDialog"><?php include(BASE . '/images/logos/copyleft.svg')?></span>
	<?php load('contact', 'copyleft')?>
</footer>
