<?php
	$github = json_decode(file_get_contents('config/github.json'));
	$PDO = new \core\PDO($github);
	$filename = filename(__FILE__);
	$start = (
		array_key_exists('commit_start', $_REQUEST)
		and is_numeric($_REQUEST['commit_start'])
	) ? (int)$_REQUEST['commit_start'] : 0;
	if($start <0) $start = 0;
	$end = $start + 10;
	$commits = $PDO->prepare("SELECT
			`SHA`,
			`Commit_URL` AS `URL`,
			`Commit_Message` AS `Message`,
			`Author_Username` AS `Author`,
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
	<button type="button" title="Previous" role="prev" data-icon="<" data-request="action=recent_commits&commit_start=<?=$start - 10?>" data-delete="#<?=$filename?>_dialog"<?=($start > 0) ? null : ' disabled';?>></button>
	<button type="button" title="Next" role="next" data-icon=">" data-request="action=recent_commits&commit_start=<?=$end?>" data-delete="#<?=$filename?>_dialog"<?=(count($commits) <10) ? ' disabled' : null;?>></button>
	<br />
	<table border="1">
		<caption>
			Recent commits to <?=$github->repository->name;?>
		</caption>
		<thead>
			<tr>
				<th>
					SHA
				</th>
				<th>
					Commit
				</th>
				<th>
					Author
				</th>
				<th>
					Timestamp
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th>
					SHA
				</th>
				<th>
					Commit
				</th>
				<th>
					Author
				</th>
				<th>
					Timestamp
				</th>
			</tr>
		</tfoot>
		<tbody>
		<?php foreach($commits as $commit):?>
			<tr>
				<td>
					<a href="<?=$commit->URL;?>" target="_blank">
						<code><?=$commit->SHA;?></code>
					</a>
				</td>
				<td>
					<details>
						<summary>
							<?=array_shift($commit->Message)?>
						</summary>
						<kbd><?=join('<br>', $commit->Message);?></kbd>
					</details>
				</td>
				<td>
					<?=$commit->Author;?>
				</td>
				<td>
					<time><?=$commit->Timestamp;?></time>
				</td>
			</tr>
		<?php endforeach;?>
		</tbody>
	</table>
</dialog>
