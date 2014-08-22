<?php
	$formname = filename(__FILE__);
?>
<dialog id="<?=$formname?>_dialog" open>
	<form name="<?=$formname?>" action="<?=URL?>/" method="post">
		<?php if(!file_exists(BASE . '/config/connect.ini')):?>
		<details open>
			<summary>User Settings</summary>
			<fieldset form="<?=$formname?>">
				<legend>Setup Database Connection</legend>
				<label for="<?=$formname?>_user" data-icon="U"></label>
				<input type="text" name="<?=$formname?>[connect][user]" id="<?=$formname?>[user]" pattern="[\w-]+" placeholder="Username" required autofocus/>
				<label for="<?=$formname?>_password" data-icon="x"></label>
				<input type="password" name="<?=$formname?>[connect][password]" id="<?=$formname?>_password" placeholder="password" required/>
				<label for="<?=$formname?>_password_repeat" data-icon="*x"></label>
				<input type="password" name="<?=$formname?>[connect][password_repeat]" id="<?=$formname?>_password_repeat" placeholder="Repeat your password" data-must-match="<?=$formname?>_password" required/>
			</fieldset>
		</details>
		<?php endif?>
		<details open>
			<summary>Root MySQL Login</summary>
			<fieldset>
				<legend>Default Admin MySQL credentials <small>For setting up database</small></legend>
				<label for="<?=$formname?>_root_user" data-icon="U"></label>
				<input type="text" name="<?=$formname?>[mysql][user]" id="<?=$formname?>_root_user" pattern="[\w-]+" placeholder="Root MySQL User" required/>
				<label for="<?=$formname?>_root_password" data-icon="x"></label>
				<input type="password" name="<?=$formname?>[mysql][password]" id="<?=$formname?>_root_password" placeholder="MySQL Root Password" required/>
			</fieldset>
		</details>
		<details open>
			<summary>Website Info</summary>
			<fieldset form="<?=$formname?>">
				<legend>Basic Website Info</legend>
				<label for="<?=$formname?>_title">Title</label>
				<input type="text" name="<?=$formname?>[head][title]" id="<?=$formname?>_title" pattern="[\w-]+" placeholder="Title of website" maxlength="50" required/><br />
				<label for="<?=$formname?>_description">Description</label>
				<textarea type="text" name="<?=$formname?>[head][description]" id="<?=$formname?>_description" placeholder="Descriptions usually appear in searches" required></textarea><br />
				<label for="<?=$formname?>_keywords">Keywords</label>
				<input type="text" name="<?=$formname?>[head][keywords]" id="<?=$formname?>_keywords" placeholder="Keywords are main topics of the site" required/><br />
				<input type="checkbox" name="<?=$formname?>[head][follow]" id="<?=$formname?>_follow" checked/>
				<label for="<?=$formname?>_follow">Should search engines follow this website?</label><br />
				<input type="checkbox" name="<?=$formname?>[head][index]" id="<?=$formname?>_index" checked/>
				<label for="<?=$formname?>_index">Should search engines index this site?</label><br />
				<label for="<?=$formname?>_author">Author</label>
				<input type="text" name="<?=$formname?>[head][author]" id="<?=$formname?>_author" placeholder="Name of website author"/><br />
				<label for="<?=$formname?>_ga">Google Analytics Code</label>
				<input type="text" name="<?=$formname?>[head][ga]" id="<?=$formname?>_ga" placeholder="Google Analytics Code"/>
				<a href="http://www.google.com/analytics/" target="_blank" title="Get one here">Get Google Analytics Code</a>
				<br />
			</fieldset>
		</details>
		<button type="submit" data-icon="."></button>
	</form>
</dialog>