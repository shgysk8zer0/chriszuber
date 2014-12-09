<?php
	$filename = filename(__FILE__);
	$github = json_decode(file_get_contents('config/github.json'));
	$PDO = new \core\PDO($github);
	$start = (
		array_key_exists('commit_start', $_REQUEST)
		and is_numeric($_REQUEST['commit_start'])
	) ? abs((int)$_REQUEST['commit_start']) : 0;

	$end = $start + 10;

	$commits = $PDO->prepare("SELECT
			`SHA`,
			`Commit_URL` AS `URL`,
			`Commit_Message` AS `Message`,
			`Author_Username` AS `Author`,
			`Author_Email` AS `Email`,
			`Time` AS `Timestamp`
		FROM `Commits`
		ORDER BY `Timestamp`
		DESC
		LIMIT {$start}, 10;
	")->execute()->get_results();

	array_walk($commits, function(&$commit) {
		$commit->Message = nl2br($commit->Message, false);
		$commit->Message = htmlentities($commit->Message, ENT_QUOTES | ENT_HTML5, 'UTF-8', true);
		$commit->Message = explode('&lt;br&gt;', $commit->Message);
	});
?>
<dialog id="<?=$filename?>_dialog">
	<button type="button" data-delete="#<?=$filename?>_dialog"></button>
	<button type="button" title="Previous" rel="prev" data-request="action=recent_commits&commit_start=<?=$start - 10?>"<?=($start > 0) ? null : ' disabled';?>></button>
	<button type="button" title="Next" rel="next" data-request="action=recent_commits&commit_start=<?=$end?>"<?=(count($commits) < 10) ? ' disabled' : null;?>></button>
	<br />
	<table border="1">
		<caption>
			Recent commits to
			<a href="<?=$github->repository->html_url?>">
				<?=$github->repository->name;?>
			</a>
		</caption>
		<thead>
			<tr>
				<th>SHA</th><th>Commit</th><th>Author</th><th>Timestamp</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th>SHA</th><th>Commit</th><th>Author</th><th>Timestamp</th>
			</tr>
		</tfoot>
		<tbody>
		<?php foreach($commits as $commit):?>
			<tr>
				<td>
					<a href="<?=$commit->URL;?>" target="_blank">
						<data value="<?=$commit->SHA;?>"><?=mb_strimwidth($commit->SHA, 0, 8);?></code>
					</a>
				</td>
				<td>
					<details>
						<summary>
							<?=array_shift($commit->Message)?>
						</summary>
						<samp><?=join('<br>', $commit->Message);?></samp>
					</details>
				</td>
				<td>
					<a href="mailto:<?=$commit->Email?>?subject=Commit <?=$commit->SHA?> on <?=$github->repository->full_name?>" target="_blank"><?=$commit->Author;?></i>
				</td>
				<td>
					<time><?=$commit->Timestamp;?></time>
				</td>
			</tr>
		<?php endforeach;?>
		</tbody>
	</table>
	<var><?=$start + 1;?></var> - <var><?=$start + count($commits);?></var>
</dialog>
