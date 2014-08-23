<?php
	$formname = filename(__FILE__);
?>
<dialog id="<?=$formname?>_dialog" open>
	<form name="<?=$formname?>" action="<?=URL?>/" method="post">
	<?php if(!file_exists(BASE . '/config/connect.ini')):?>
		<?php if(!is_readable(BASE . '/config/')):?>
		<strong><code>config/</code> directory cannot be written to.</strong>
		<p>
			Please change permissions using
			<pre><code>
chmod -R g+w config
chgrp -R www-data config
			</code></pre>
		or manually create <code>config/connect.ini</code> using
		<pre><code>
user = "{username}"
password = "{password}"
		</code></pre>
		</p>
		<?php else:?>
		<fieldset form="<?=$formname?>">
			<legend>Database Connection Settings</legend>
			<label for="<?=$formname?>_connect_user" data-icon="U"></label>
			<input type="text" name="<?=$formname?>[connect][user]" id="<?=$formname?>_connect_user" placeholder="Username" required/><br />
			<label for="<?=$formname?>_connect_password" data-icon="x"></label>
			<input type="password" name="<?=$formname?>[connect][password]" id="<?=$formname?>_connect_password" placeholder="Password"/><br />
			<label for="<?=$formname?>_connect_repeat" data-icon="*"></label>
			<input type="password" name="<?=$formname?>[connect][repeat]" id="<?=$formname?>_connect_repeat" data-must-match="<?=$formname?>[connect][password]" placeholder="Repeat Password" required/>
		</fieldset>
		<?php endif?>
	<?php endif?>
		<fieldset form="<?=$formname?>">
			<legend>Root MySQL User</legend>
			<label for="<?=$formname?>_root_useer" data-icon="U"></label>
			<input type="text" name="<?=$formname?>[root][user]" id="<?=$formname?>_root_user" placeholder="Root MySQL User" required/><br >
			<label for="<?=$formname?>_root_password" data-icon="x"></label>
			<input type="password" name="<?=$formname?>[root][password]" id="<?=$formname?>_root_password" placeholder="Root MySQL Password" required/>
		</fieldset>
		<button type="submit" data-icon="."></button>
	</form>
</dialog>