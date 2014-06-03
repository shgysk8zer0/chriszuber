<?php
	$session = session::load();
	require_once('./custom.php');
?>
<form name="tag_search" method="GET" action="<?=URL?>" autocomplete="off">
	<input type="search" name="tags" placeholder="Search for tags" list="tags" pattern="[\w ]+" required/>
	<!--<input type="hidden" name="nonce" value="<?=$session->nonce?>" required readonly/>-->
	<button type="submit" data-icon="L"></button>
</form>
