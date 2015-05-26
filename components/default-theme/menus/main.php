<?php
	$posts = get_recent_posts(10);
?>
<menu type="context" id="main_menu">
	<menuitem label="Scroll to top" icon="images/octicons/svg/jump-up.svg" data-scroll-to="body > header"></menuitem>
	<menuitem label="Scroll to bottom" icon="images/octicons/svg/jump-down.svg" data-scroll-to="body > footer"></menuitem>
	<menuitem label="Go to comments section" icon="images/octicons/svg/comment.svg" data-scroll-to="#comments_section"></menuitem>
	<menu label="Posts">
		<menuitem label="Home" icon="images/icons/home.svgz" data-link="<?=URL?>" data-cache="home"></menuitem>
		<?php foreach($posts as $post):?>
		<menuitem label="<?=$post->title?>" icon="images/icons/coffee.svgz" data-link="<?=URL?>posts/<?=$post->url?>"></menuitem>
		<?php endforeach?>
	</menu>
	<menu label="Account">
	<?php if($login->logged_in):?>
		<menuitem label="Login" icon="images/icons/people.svgz" data-show-modal="#loginDialog" disabled></menuitem>
		<menuitem label="Logout" icon="images/icons/people.svgz" data-request="action=logout"></menuitem>
	<?php else:?>
		<menuitem label="Login" icon="images/icons/people.svgz" data-show-modal="#loginDialog"></menuitem>
		<menuitem label="Logout" icon="images/icons/people.svgz" data-request="action=logout" disabled></menuitem>
	<?php endif?>
	</menu>
</menu>
