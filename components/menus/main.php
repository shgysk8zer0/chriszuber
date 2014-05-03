<?php
	$session = session::load();
	$storage = storage::load();
?>
<menu type="context" id="posts_menu">
	<menu label="Posts">
		<menuitem label="about" data-request="load=contact_card"></menuitem>
	</menu>
</menu>
