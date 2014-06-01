<?php
	$session = session::load();
?>
<form name="new_post" action="." method="post">
	<fieldset form="new_post">
		<legend>New Post</legend>
		<article>
			<header>
				<h1 contenteditable="true" data-input-name="title" itemprop="headline">TITLE</h1>
				<nav contenteditable="true" data-input-name="keywords">
					KEYWORDS
				</nav>
			</header>
			<section contenteditable="true" data-input-name="content" itemprop="text">Article Content Here</section>
		</article>
		<label for="description">Description: </label>
		<textarea name="description" id="description"></textarea><br/>
		<!--<input type="text" name="description" id="description" required/><br/>-->
		<input type="hidden" name="nonce" value="<?=$session->nonce?>" required readonly/>
		<input type="submit" value="Submit"/>
		<input type="reset" value="Reset"/>
	</fieldset>
</form>
