<?php
	$session = session::load();
	$login = login::load();
?>
<form name="comments" method="post" action="<?=URL?>">
	<fieldset form="comments">
		<legend>Comment on post</legend>
		<textarea name="comment" placeholder="No HTML or other markeup for now, please." required></textarea><br/>
		<input type="hidden" name="nonce" value="<?=$session->nonce?>" require readonly/>
		<input type="hidden" name="post_on" value="<?=$session->nonce?>" require readonly/>
		<button type="submit" data-icon=">"></button>
	</fieldset>
</form>
