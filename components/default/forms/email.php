<?php
	$formname = filename(__FILE__);
?>
<dialog id="<?=$formname?>_dialog">
	<form name="<?=$formname?>" action="<?=URL?>/" method="post">
		<fieldset form="<?=$formname?>">
			<label for="<?=$formname?>[to]" data-icon="@" title="to"></label>
			<input type="email" name="<?=$formname?>[to]" id="<?=$formname?>[to]" placeholder="To: user@example.com" multiple autofocus required/><br />
			<details>
				<summary>
					More:
				</summary>
				<label for="<?=$formname?>[cc]" data-icon="@" title="CC"></label>
				<input type="email" name="<?=$formname?>[cc]" id="<?=$formname?>[cc]" placeholder="CC: user@example.com" multiple/><br />

			</details>
			<label for="<?=$formname?>[subject]">Subject</label>
			<input type="text" name="<?=$formname?>[subject]" id="<?=$formname?>[subject]" placeholder="Subject" required/>
			<div contenteditable="true" contextmenu="wysiwyg_menu" class="resizeable" data-input-name="<?=$formname?>[message]" data-dropzone="#<?=$formname?>_dialog"></div>
		</fieldset>
		<button type="submit" data-icon="."></button>
		<button type="button" data-delete="#<?=$formname?>_dialog"></button>
	</form>
</dialog>
