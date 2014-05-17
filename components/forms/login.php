<?php
	$session = session::load();
?>
<form name="login" action="." method="post">
	<fieldset form="login">
		<legend>Login</legend>
		<label for="user">Email: </label>
		<input type="email" name="user" id="user" placeholder="user@exampple.com" required/>
		<label for="password">Password: </label>
		<input type="password" name="password" id="password" pattern="<?=pattern('password')?>" required/>
		<input type="hidden" name="nonce" value="<?=$session->nonce?>" required readonly/>
		<input type="submit" value="Login"/>
		<input type="reset" value="Clear"/>
	</fieldset>
</form>