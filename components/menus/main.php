<?php
	$session = session::load();
	$login = login::load();
?>
<menu type="context" id="main_menu">
	<menu label="Posts">
		<menuitem label="about" data-request="load=contact_card"></menuitem>
	</menu>
	<menu label="Account">
	<?php if($login->logged_in):?>
		<menuitem label="Login" icon="images/icons/people.svgz" data-request="load_form=login" disabled></menuitem>
		<menuitem label="Logout" icon="images/icons/people.svgz" data-request="action=logout"></menuitem>
	<?php else:?>
		<menuitem label="Login" icon="images/icons/people.svgz" data-request="load_form=login"></menuitem>
		<menuitem label="Logout" icon="images/icons/people.svgz" data-request="action=logout" disabled></menuitem>
	<?php endif?>
	</menu>
</menu>
