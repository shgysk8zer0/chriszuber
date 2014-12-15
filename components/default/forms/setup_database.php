<?php
	$connect = \core\resources\Parser::parse('connect.json');
?>
<form name="setup_database" action="<?=URL?>/" method="post">
	<fieldset form="setup_database">
		<legend>Root MySQL user</legend>
		<label for="username">User: </label>
		<input type="text" name="username" id="username" value="root" placeholder="Default MySQL user" required/><br />
		<label for="password">Password</label>
		<input type="password" name="password" id="password" placeholder="Default user's password"/>
		<button type="submit" title="Submit Form" data-icon="."></button>
		<button type="reset" data-icon="V" title="Clear Form"></button>
		<details>
			<summary><b>Options</b></summary>
			<label for="server">Server Address</label>
			<input type="text" name="server" id="server" value="localhost" required/>
		</details>
	</fieldset>
</form>
