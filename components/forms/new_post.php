<?php
	$session = session::load();
?>
<form name="new_post" action="." method="post">
	<fieldset form="new_post">
		<legend>New Post</legend>
		<article>
			<header>
				<h1 contenteditable="true" data-input-name="title" itemprop="headline">%TITLE%</h1>
				<nav contenteditable="true" data-input-name="keywords">
					%TAGS%
				</nav>
			</header>
			<section contenteditable="true" data-input-name="content" itemprop="text">%CONTENT%</section>
		</article>
		<!--<label for="title">Title: </label>
		<input type="text" name="title" id="title" required/><br/>-->
		<label for="description">Description: </label>
		<input type="text" name="description" id="description" required/><br/>
		<!--<label for="keywords">Keywords: </label>
		<input type="text" name="keywords" id="keywords" required/><br/>-->
		<label for="author">Author: </label>
		<input type="text" name="author" id="author" required/><br/>
		<!--<label for="content">Post: </label>
		<textarea name="content" id="content" required></textarea><br/>-->
		<input type="hidden" name="nonce" value="<?=$session->nonce?>" required readonly/>
		<input type="submit" value="Submit"/>
		<input type="reset" value="Reset"/>
	</fieldset>
</form>
