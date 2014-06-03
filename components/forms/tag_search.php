<?php
	$session = session::load();
	require_once('./custom.php');
?>
<form name="tag_search" method="POST" action="<?=URL?>" autocomplete="off">
	<input type="search" name="tags" placeholder="Search for tags" list="tags" pattern="[\w\-]+" required/>
	<button type="submit" data-icon="L"></button>
</form>
