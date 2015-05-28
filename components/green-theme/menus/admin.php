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

	$connect = \shgysk8zer0\Core\resources\parser::parseFile('connect.json');

	$tables = $DB->showTables();
?>
<menu type="context" id="admin_menu">
	<menu label="Post Management">
		<menuitem label="New Post" icon="images/octicons/svg/pencil.svg" data-request="load_form=new_post"></menuitem>
		<menuitem label="Edit Post" icon="images/octicons/svg/file-text.svg"></menuitem>
		<menuitem label="Update Sitemap" icon="images/octicons/svg/file-code.svg" data-request="action=update_sitemap"></menuitem>
		<menuitem label="Update RSS" icon="images/octicons/svg/rss.svg" data-request="action=update_rss"></menuitem>
	</menu>
	<?php if($login->role === 'admin'):?>
	<menu label="PHP Defaults">
			<menuitem type="command" label="_SERVER" icon="images/octicons/svg/tools.svg" data-request="debug=_SERVER"></menuitem>
			<menuitem type="command" label="_SESSION" icon="images/octicons/svg/tools.svg" data-request="debug=_SESSION"></menuitem>
			<menuitem type="command" label="_COOKIE" icon="images/octicons/svg/tools.svg" data-request="debug=_COOKIE"></menuitem>
			<menuitem type="command" label="Headers" icon="images/octicons/svg/tools.svg" data-request="debug=headers"></menuitem>
			<menuitem type="command" label="PHP Extensions" icon="images/octicons/svg/tools.svg" data-request="debug=extensions"></menuitem>
			<menuitem type="command" label="Apache Modules" icon="images/octicons/svg/tools.svg" data-request="debug=modules"></menuitem>
			<menuitem type="command" label="PHP Variables" icon="images/octicons/svg/tools.svg" data-request="debug=vars"></menuitem>
		</menu>
	<menu label="Manage Database">
		<menuitem type="command" label="Restore Database" icon="images/octicons/svg/database.svg" data-request="action=restore database" data-confirm="Are you sure you want to restore the database from <?=$connect->database?>.sql? All changes made since the last mysqldump will be reverted."></menuitem>
		<menuitem type="command" label="Backup Database" icon="images/octicons/svg/database.svg" data-request="action=backup database" data-confirm="Are you sure you want to backup the database to <?=$connect->database?>.sql?"></menuitem>
		<menuitem type="command" label="Clear PHP Errors" icon="images/octicons/svg/bug.svg" data-request="action=Clear PHP_errors"></menuitem>
		<menuitem type="command" label="Clear CSP" icon="images/octicons/svg/bug.svg" data-request="reset_table=CSP_errors"></menuitem>
		</menu>
	<menuitem type="command" label="Search PHP errors" icon="images/octicons/svg/search.svg" data-request="load_form=php_errors"></menuitem>
	<menuitem type="command" label="Update Icons" icon="images/octicons/svg/sync.svg" data-request="action=update_icons"></menuitem>
	<menuitem type="command" label="Compose email" icon="images/octicons/svg/mail.svg" data-request="load_form=compose_email"></menuitem>
	<menuitem type="command" label="test" icon="images/octicons/svg/terminal.svg" data-request="action=test"></menuitem>
	<?php endif?>
	<menuitem type="command" label="Clear Cache" icon="images/octicons/svg/trashcan.svg" data-confirm="Are you sure you want to clear your cache for this site?"></menuitem>
</menu>
