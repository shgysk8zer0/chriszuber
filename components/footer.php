	<footer>
		<?php foreach($DB->fetch_array("SELECT `url`, `icon` FROM `footer_links` ORDER BY `order` ASC") as $link):?>
		<a href="<?=$link->url?>" target="_blank" class="logo">
			<img src="<?=URL?>/images/<?=$link->icon?>" alt="<?=$link->url?>"/>
		</a>
		<?php endforeach?>
	</footer>
