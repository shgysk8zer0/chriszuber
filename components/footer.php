<footer>
	<?php load('copyleft')?>
	<?php foreach($DB->fetch_array("SELECT `url`, `icon`, `alt` FROM `footer_links` ORDER BY `order` ASC") as $link):?>
	<a href="<?=$link->url?>" target="_blank" class="logo" title="<?=$link->alt?>">
		<!--<img src="<?=URL?>/images/<?=$link->icon?>" alt="<?=$link->alt?>"/>-->
		<?php include(BASE . "/images/{$link->icon}")?>
	</a>
	<?php endforeach?>
	<span title="Copyleft" class="copyleft" data-show-modal="#copyleftDialog"></span>
</footer>
