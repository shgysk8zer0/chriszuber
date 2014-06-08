<?php
	$session = session::load();
	require_once('./custom.php');
?>
<form name="tag_search" method="POST" action="<?=URL?>" role="search" autocomplete="off">
	<input type="search" name="tags" placeholder="Search for tags" list="tags" pattern="[\w\-]+" rel="search" required/>
	<button type="submit" data-icon="L"></button>
</form>
