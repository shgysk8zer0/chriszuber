<?php
	$session = session::load();
	$storage = storage::load();
?>
<menu type="context" id="main_menu">
	<menu label="Posts">
		<menuitem label="about" data-request="load=contact_card"></menuitem>
	</menu>
	<menu label="Account">
		<menuitem label="Login" icon="images/icons/people.svgz" data-request="load_form=login"></menuitem>
		<menuitem label="Logout" icon="images/icons/people.svgz" data-request="action=logout" disabled></menuitem>
	</menu>
</menu>
