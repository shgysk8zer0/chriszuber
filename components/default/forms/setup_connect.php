<?php
	if(@file_exists(BASE . '/config/test.ini')) {
		exit('<strong>Loading form to create connection file, but it already exists</strong>');
	}
?>
<form name="setup_connect" action="<?=URL?>" method="post">
	<fieldset form="setup_connect">
		<legend>Database Configuragtions</legend>
		<label for="user" data-icon="U">Username</label>
		<input type="text" name="user" id="user" value="<?=end(explode('/', BASE))?>" autofocus required/><br />
		<label for="password" data-icon="x">Password</label>
		<input type="password" name="password" id="password" pattern="<?=pattern('password')?>" required/><br />
		<button type="submit" title="Submit Form" data-icon="."></button>
		<button type="reset" data-icon="V" title="Clear Form"></button>
		<details>
			<summary><b>Options</b></summary>
			<label for="server">Server Address</label>
			<input type="text" name="server" id="server" value="localhost" pattern="(\d{1,3}\.\d{1,3}\.\d{1,3})|(localhost)" required/><br />
			<label for="database">Database</label>
			<input type="text" name="database" id="database" value="<?=end(explode('/', BASE))?>" pattern="[A-z]{5,15}" required/>
		</details>
	</fieldset>
</form>
