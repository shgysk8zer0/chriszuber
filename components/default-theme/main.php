<?php
	$settings = \shgysk8zer0\Core\resources\Parser::parseFile('settings.json');
	if (
		isset($settings->module_test)
		and $settings->module_test
	) {
		$missing = module_test($settings);
	}
	if ($DB->connected) {
		$pages = \shgysk8zer0\Pages::load();
	} else {
		$pages = new \stdClass();
		$pages->content = null;
	}
?>
<main role="main" itemprop="mainContentofPage" itemscope itemtype="http://schema.org/WebPageElement" id="main" class="flex row wrap" <?=($login->logged_in and $login->role === 'admin') ? ' contextmenu="admin_menu"' : ''?>>
	<?php
		if (isset($missing)) {
			echo '<div data-error="Missing Modules"><strong>Missing PHP Modules</strong><ul>';
			foreach ($missing->php as $php) {
				echo "<li>{$php}</li>";
			}
			echo '</ul><strong>Missing Apache Modules</strong><ul>';
			foreach ($missing->apache as $apache) {
				echo "<li>{$apache}</li>";
			}
			echo "</ul></div>";

		} elseif ($DB->connected) {
			echo $pages->content;
		}
		load('sidebar');
	?>
</main>
<?php ob_flush(); flush();?>
