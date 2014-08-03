<form name="new_post" action="<?=URL?>" method="post">
	<fieldset form="new_post">
		<legend>New Post</legend>
		<article contextmenu="wysiwyg_menu">
			<header>
				<h1 itemprop="headline"><input type="text" name="title" autocomplete="off" placeholder="Title" required/></h1>
				<nav><input type="text" name="keywords" list="tags" autocomplete="off" placeholder="Keywords" requied/></nav>
			</header>
			<section contenteditable="true" data-input-name="content" itemprop="text" data-dropzone="main" data-menu="wysiwyg">Article Content Here</section>
		</article>
		<label for="description">Description: </label>
		<textarea name="description" id="description" placeholder="Description will appear in searches. 160 character limit" maxlength="160" required></textarea><br/>
		<button type="submit" title="Create Post" data-icon="."></button>
	</fieldset>
</form>
