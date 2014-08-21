<footer>
	<?php foreach($DB->fetch_array("SELECT `url`, `icon`, `alt` FROM `footer_links` ORDER BY `order` ASC") as $link):?>
	<a href="<?=$link->url?>" target="_blank" class="logo" title="<?=$link->alt?>">
		<?php include(BASE . "/images/{$link->icon}")?>
	</a>
	<?php endforeach?>
	<?php load('contact', 'copyleft')?>
	<span title="Copyleft" class="copyleft logo" data-show-modal="#copyleftDialog"></span>
	<img class="logo" src="images/icons/info.svgz" title="Show README" alt="Show README" data-show-modal="#README"/>
</footer>
