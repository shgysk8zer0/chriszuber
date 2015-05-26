<menu type="context" id="main_menu">
	<menuitem label="Scroll to top" icon="images/octicons/svg/jump-up.svg" data-scroll-to="body > header"></menuitem>
	<menuitem label="Scroll to bottom" icon="images/octicons/svg/jump-down.svg" data-scroll-to="body > footer"></menuitem>
	<menuitem label="Go to comments section" icon="images/octicons/svg/comment-discussion.svg" data-scroll-to="#comments_section"></menuitem>
	<menuitem label="Go to recent posts" icon="images/octicons/svg/book.svg" data-scroll-to=".recent.posts"></menuitem>
	<menuitem label="Go to recent tags" icon="images/octicons/svg/tag.svg" data-scroll-to=".recent.tags"></menuitem>
	<hr>
	<?php if($login->logged_in):?>
		<menuitem label="Login" icon="images/octicons/svg/sign-in.svg" data-show-modal="#loginDialog" disabled></menuitem>
		<menuitem label="Logout" icon="images/octicons/svg/sign-out.svg" data-request="action=logout"></menuitem>
	<?php else:?>
		<menuitem label="Login" icon="images/octicons/svg/sign-in.svg" data-show-modal="#loginDialog"></menuitem>
		<menuitem label="Logout" icon="images/octicons/svg/sign-out.svg" data-request="action=logout" disabled></menuitem>
	<?php endif;?>
</menu>
