<?php
	$github = json_decode(file_get_contents('config/github.json'));
	$PDO = new \core\PDO($github->database);
	$filename = filename(__FILE__);

	$table = new \core\table('SHA', 'Commit', 'Author', 'Timestamp');
	$table->caption = 'Recent Commits';

	array_map(function($commit) use (&$table) {
		$table->SHA(
		$commit->SHA
		)->Commit(
		"<a href=\"{$commit->URL}\" target=\"_blank\">{$commit->Message}</a>"
		)->Author(
		$commit->Author
		)->Timestamp(
		$commit->Timestamp
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
	<button type="button" data-close="#<?=$filename?>_dialog"></button>
	<?=$table->out(true, true);?>
</dialog>
