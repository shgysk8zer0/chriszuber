<?php
	$settings = ini::load('settings');
	if(
		isset($settings->module_test)
		and $settings->module_test
	) {
		$missing = module_test();
	}
	if($DB->connected) {
		$pages = pages::load();
	}
	else {
		$pages = new stdClass();
		$pages->content = null;
	}
?>
<main role="main" itemprop="mainContentofPage" itemscope itemtype="http://schema.org/Blog" <?=($login->logged_in and $login->role === 'admin') ? ' contextmenu="admin_menu"' : ''?>>
	<?php
		load('sidebar');
		if(isset($missing)) {
			echo '<div data-error="Missing Modules"><strong>Missing PHP Modules</strong><ul>';
			foreach($missing->php as $php) {
				echo "<li>{$php}</li>";
			}
			echo '</ul><strong>Missing Apache Modules</strong><ul>';
			foreach($missing->apache as $apache) {
				echo "<li>{$apache}</li>";
			}
			echo "</ul></div>";

		}
		elseif($DB->connected) {
			echo $pages->content;
		}
	?>
</main>
