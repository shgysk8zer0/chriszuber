<footer>
	<?php foreach($DB->fetch_array("SELECT `url`, `icon`, `alt` FROM `footer_links` ORDER BY `order` ASC") as $link):?>
	<a href="<?=$link->url?>" target="_blank" class="logo" title="<?=$link->alt?>">
		<?php include(BASE . "/images/{$link->icon}")?>
	</a>
	<?php endforeach?>
	<?php load('contact', 'copyleft')?>
	<span title="Copyleft" class="copyleft" data-show-modal="#copyleftDialog"></span>
</footer>
