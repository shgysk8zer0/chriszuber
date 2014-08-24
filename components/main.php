<?php
	if($DB->connected) {
		$pages = pages::load();
	}
	else {
		$pages = new stdClass();
		$pages->content = null;
	}
?>
<main role="main" itemprop="mainContentofPage" itemscope itemtype="http://schema.org/Blog" <?=($login->logged_in) ? ' contextmenu="admin_menu"' : ''?>>
	<?php load('sidebar')?>
	<?=($DB->connected) ? $pages->content : null?>
</main>
