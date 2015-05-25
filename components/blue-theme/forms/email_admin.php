<?php
	$formname = filename(__FILE__);
?>
<dialog id="<?=$formname?>_dialog">
	<form name="<?=$formname?>" action="<?=URL?>" method="post">
		<fieldset form="<?=$formname?>">
			<legend>
				Compose new email
			</legend>
			<label for="<?=$formname?>[from]" data-icon="@">From</label>
			<input type="email" name="<?=$formname?>[from]" placeholder="user@example.com" required/><br />
			<label for="<?=$formname?>[subject]">Subject</label>
			<input type="text" name="<?=$formname?>[subject]" id="<?=$formname?>[subject]" placeholder="Subject" required/><br />
			<!--<div contenteditable="true" contextmenu="wysiwyg_menu" class="resizeable" data-input-name="<?=$formname?>[message]" data-dropzone="#<?=$formname?>_dialog"></div>-->
			<textarea name="<?=$formname?>[message]" placeholder="No HTML Allowed" required></textarea>
		</fieldset>
		<button type="submit" title="Send" data-icon="."></button>
		<button type="button" title="Close Dialog" data-close="#<?=$formname?>_dialog"></button>
	</form>
</dialog>
