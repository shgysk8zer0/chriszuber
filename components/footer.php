<?php $storage = storage::load()?>
	<footer>
		<a rel="license" href="http://creativecommons.org/licenses/by-sa/4.0/"><img alt="Creative Commons License" src="images/logos/CreativeCommons.svgz" /></a><br /><span xmlns:dct="http://purl.org/dc/terms/" property="dct:title"><?=$storage->site_info->title?></span> by <a xmlns:cc="http://creativecommons.org/ns#" href="<?=$storage->site_info->author_g_plus?>?rel=author" property="cc:attributionName" rel="cc:attributionURL"><?=$storage->site_info->author?></a> is licensed under a <a rel="license" href="http://creativecommons.org/licenses/by-sa/4.0/">Creative Commons Attribution-ShareAlike 4.0 International License</a>.
		<a href="https://github.com/shgysk8zer0/chriszuber" target="_blank" class="logo confirm">
			<?php require('images/logos/github.svg')?>
		</a>
		<?php debug($storage)?>
	</footer>
