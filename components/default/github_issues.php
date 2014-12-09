<?php
	$file = filename(__FILE__);
	$github = json_decode(file_get_contents('config/github.json'));
	$PDO = new \core\PDO($github);
	if(!$PDO->connected) exit();
	$issues = $PDO->prepare("SELECT `Number`,
			`Title`,
			`Body`,
			`URL`,
			`Labels`,
			`Assignee`,
			`Milestone`,
			`Milestone_URL`,
			`Created_At` AS `Created`,
			`Updated_At` AS `Updated`
		FROM `Issues`
		WHERE `State` = :state
		AND `Repository` = :repo
	")->bind([
		'state' => 'open',
		'repo' => $github->repository->full_name
	])->execute()->get_results();

	$new_issue = "<a href=\"https://github.com/{$github->repository->full_name}/issues/new\" target=\"_blank\" title=\"New\" role=\"button\" data-icon=\"+\"></a>";
?>
<dialog id="<?=$file?>_dialog">
	<button type="button" data-delete="#<?=$file?>_dialog"></button><br />
	<table border="1">
		<caption>
			Open Issues on <?=$github->repository->name;?> <?=$new_issue;?><br /><br />
		</caption>
		<thead>
			<tr>
				<th>Number</th>
				<th>Issue</th>
				<th>Created</th>
				<th>Updated</th>
				<th>Labels</th>
				<th>Assignee</th>
				<th>Milestone</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th>Number</th>
				<th>Issue</th>
				<th>Created</th>
				<th>Updated</th>
				<th>Labels</th>
				<th>Assignee</th>
				<th>Milestone</th>
			</tr>
		</tfoot>
		<tbody>
		<?php foreach($issues as $issue):?>
			<tr>
				<td>
					<a href="<?=$issue->URL;?>" target=\"_blank\"><?=$issue->Number;?></a>
				</td>
				<td>
					<details>
						<summary><?=utf($issue->Title);?></summary>
						<samp><?=nl2br(utf($issue->Body));?></samp>
					</details>
				</td>
				<td>
					<time><?=date('D, M jS Y g:i A', strtotime($issue->Created));?></time>
				</td>
				<td>
					<time><?=date('D, M jS Y g:i A', strtotime($issue->Updated));?></time>
				</td>
				<td>
					<?=$issue->Labels;?>
				</td>
				<td>
					<?=$issue->Assignee;?>
				</td>
				<td>
					<a href="<?=$issue->Milestone_URL;?>" target="_blank"><?=$issue->Milestone;?></a>
				</td>
			</tr>
		<?php endforeach;?>
		</tbody>
	</table>
</dialog>
