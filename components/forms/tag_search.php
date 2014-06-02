<?php
	$session = session::load();
	$keywords = flatten($DB->fetch_array("
		SELECT `keywords` FROM `posts`
	"));
	$tags = [];
	foreach($keywords as $keyword) {
		foreach(explode(',', $keyword) as $tag) {
			$tags[] = trim($tag);
		}
	};
?>
<form name="tag_search" method="POST" actiion="<?=URL?>" autocomplete="off">
	<input type="search" name="tag" placeholder="Search for tags" list="tags" pattern="\w+" required/>
	<input type="hidden" name="nonce" value="<?=$session->nonce?>" required readonly/>
	<button type="submit" data-icon="L"></button>
	<datalist id="tags">
		<?php foreach(array_unique($tags) as $tag): ?>
		<option value="<?=$tag?>"><?=$tag?></option>
		<?php endforeach?>
	</datalist>
</form>
