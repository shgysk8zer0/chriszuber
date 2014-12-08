<?php
	$github = json_decode(file_get_contents('config/github.json'));
	$PDO = new \core\PDO($github->database);
	$filename = filename(__FILE__);

	$table = new \core\table('SHA', 'Commit', 'Author', 'Timestamp');
	$table->caption = 'Recent Commits';

	array_map(function($commit) use (&$table) {
		$commit->Message = nl2br($commit->Message, false);
		$commit->Message = htmlentities($commit->Message, ENT_QUOTES | ENT_HTML5, 'UTF-8', true);
		$commit->Message = explode('&lt;br&gt;', $commit->Message);
		$table->SHA(
			"<code>{$commit->SHA}</code>"
		)->Commit(
			'<details><summary>' . array_shift($commit->Message) . '</summary>
			<a href="' . $commit->URL .'" target="_blank">'
			. join('<br>', $commit->Message) . '</a></details>'
		)->Author(
			$commit->Author
		)->Timestamp(
			"<time>{$commit->Timestamp}</time>"
		)->next_row();
	}, $PDO->fetch_array("
		SELECT
			`SHA`,
			`Commit_URL` AS `URL`,
			`Commit_Message` AS `Message`,
			`Author_Username` AS `Author`,
			`Time` AS `Timestamp`
		FROM `Commits`
		ORDER BY `Timestamp`
		DESC
		LIMIT 10
	"));
?>
<dialog id="<?=$filename?>_dialog">
	<button type="button" data-delete="#<?=$filename?>_dialog"></button>
	<?=$table->out(true, true);?>
</dialog>
