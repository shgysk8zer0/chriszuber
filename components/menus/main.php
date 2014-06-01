<?php
	$session = session::load();
	$login = login::load();
	$posts = $DB->fetch_array("
		SELECT `title`, `url`
		FROM `posts`
		WHERE `url` != ''
		ORDER BY `created`
		LIMIT 10
	");
?>
<menu type="context" id="main_menu">
	<menu label="Posts">
		<menuitem label="Home" icon="images/icons/home.svgz" data-link="<?=URL?>"></menuitem>
		<?php foreach($posts as $post):?>
		<menuitem label="<?=$post->title?>" icon="images/icons/coffee.svgz" data-link="<?=URL?>/posts/<?=$post->url?>"></menuitem>
		<?php endforeach?>
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
