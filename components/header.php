<?php
	$session = session::load();
	$storage = storage::load();
?>
	<header>
		<h1><?=$storage->site_info->title?></h1>
		<nav><button type="button" data-request="load=contact_card" data-icon="U"></button></nav>
	</header>
