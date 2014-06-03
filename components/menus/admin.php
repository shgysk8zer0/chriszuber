<?php
	/**
	* These menus are for HTML5 context menus, which
	* is currently only supported in Firefox, and unfortunately
	* uses <menuitem> instead of the correct <command> tag
	*
	* It is only available to a logged-in user with the role of admin
	*
	* It provides debugging information as well as database restoring options.
	*
	* Instructions for creating new menuitems...

	* type=command is optional, much like <input type=text>
	* label is the text used in the context menu, and icon is used as the icon
	* data-target tells $.ajax() where to put the ajax response
	* data-request is the ajax request in the post.
	* data-url is optional, and sends the request elsewhere (defaults to './')

	* Listeners are handled automatically by $('[data-target][data-request]').click listeners,
	* and Mutation Observers handle applying the listeners for ajax requests.
	*/

	$session = session::load();
	$pdo = _pdo::load();
	$login = login::load();
	$connect = ini::load('connect');

	$tables = $pdo->show_tables();
?>
<menu type="context" id="admin_menu">
	<menu label="Post Management">
		<menuitem label="New Post" icon="images/icons/db.svgz" data-request="load_form=new_post"></menuitem>
		<menuitem label="Edit Post" icon="images/icons/db.svgz"></menuitem>
	</menu>
	<?php if($login->role === 'admin'):?>
	<menu label="PHP Defaults">
			<menuitem type="command" label="_SERVER" icon="images/icons/coffee.svgz" data-request="debug=_SERVER&nonce=<?=$session->nonce?>" data-cache="debug_server"></menuitem>
			<menuitem type="command" label="_SESSION" icon="images/icons/coffee.svgz" data-request="debug=_SESSION&nonce=<?=$session->nonce?>"></menuitem>
			<menuitem type="command" label="_COOKIE" icon="images/icons/coffee.svgz" data-request="debug=_COOKIE&nonce=<?=$session->nonce?>"></menuitem>
			<menuitem type="command" label="Headers" icon="images/icons/coffee.svgz" data-request="debug=headers&nonce=<?=$session->nonce?>" data-cache="debug_headers"></menuitem>
			<menuitem type="command" label="PHP Extensions" icon="images/icons/coffee.svgz" data-request="debug=extensions&nonce=<?=$session->nonce?>" data-cache="debug_extensions"></menuitem>
			<menuitem type="command" label="Apache Modules" icon="images/icons/coffee.svgz" data-request="debug=modules&nonce=<?=$session->nonce?>" data-cache="debug_modules"></menuitem>
		</menu>
	<menu label="Manage Database">
		<menuitem type="command" label="Restore Database" icon="images/icons/db.svgz" data-request="action=restore database&nonce=<?=$session->nonce?>" data-confirm="Are you sure you want to restore the database from <?=$connect->database?>.sql? All changes made since the last mysqldump will be reverted."></menuitem>
		<menuitem type="command" label="Backup Database" icon="images/icons/db.svgz" data-request="action=backup database&nonce=<?=$session->nonce?>" data-confirm="Are you sure you want to backup the database to <?=$connect->database?>.sql?"></menuitem>
		<menuitem type="command" label="Clear CSP" icon="images/icons/db.svgz" data-request="reset_table=CSP_errors&nonce=<?=$session->nonce?>"></menuitem>
		<menuitem type="command" label="MySQL Query" icon="images/icons/db.svgz" data-request="action=mysql_query&nonce=<?=$session->nonce?>" data-prompt="Type your query:"></menuitem>
		</menu>
	<menuitem type="command" label="test" icon="images/icons/coffee.svgz" data-request="action=test"></menuitem>
	<?php endif?>
	<menuitem type="command" label="Clear Cache" icon="images/icons/settings.svgz" data-confirm="Are you sure you want to clear your cache for this site?"></menuitem>
</menu>