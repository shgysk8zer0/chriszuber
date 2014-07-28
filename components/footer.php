	<footer>
		<?php foreach($DB->fetch_array("SELECT `url`, `icon`, `alt` FROM `footer_links` ORDER BY `order` ASC") as $link):?>
		<a href="<?=$link->url?>" target="_blank" class="logo" title="<?=$link->alt?>">
			<img src="<?=URL?>/images/<?=$link->icon?>" alt="<?=$link->alt?>"/>
		</a>
		<?php endforeach?>
		<a href="https://www.gnu.org/copyleft/" class="copyleft" title="Copyleft"><?=date('Y')?></a>
	</footer>
