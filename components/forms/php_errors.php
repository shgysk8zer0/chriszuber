<?php
	$E_consts = flatten($DB->fetch_array("
		SELECT DISTINCT `error_type` FROM `PHP_errors`
	"));
?>
<form name="php_errors" action="." method="post">
	<?php if(count($E_consts)):?>
	<fieldset form="php_errors">
		<legend data-icon="L">PHP Errors</legend>
	<?php else:?>
		<fieldset form="php_errors" disabled>
			<legend data-icon="L">No PHP Errors</legend>
	<?php endif?>
		<label for="file">File</label>
		<input type="search" name="file" id="file" pattern="[A-z\/]+\.php" placeholder="path/to/file.ext" list="PHP_errors_files" autocomplete="off" required/>
		<label for="level">Type</label>
		<select name="level">
			<option value="*">Any</option>
			<?php foreach($E_consts as $lvl):?>
			<option value="<?=$lvl?>"><?=$lvl?></option>
			<?php endforeach?>
		</select>
		<input type="hidden" name="nonce" value="<?=$_SESSION['nonce']?>" required readonly/>
		<button type="submit" data-icon="L"></button>
		<button type="reset" data-icon="V" title="Clear Form"></button>
	</fieldset>
</form>
