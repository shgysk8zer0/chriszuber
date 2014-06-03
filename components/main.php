<?php
	$connect = ini::load('connect');
	$login = login::load();
	$pages = pages::load();
?>
<main role="main" itemprop="mainContentofPage" itemscope itemtype="http://schema.org/Blog" <?=($login->logged_in) ? ' data-menu="admin"' : ''?>>
	<?=$pages->content?>
	<?php load('sidebar')?>
</main>
