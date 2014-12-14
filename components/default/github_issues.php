<?php
	$file = filename(__FILE__);
	$github = \core\resources\Parser::parse('github.json');
	$PDO = new \core\PDO($github);
	if(!$PDO->connected) exit();
	$issues = $PDO->prepare("SELECT `Number`,
			`Repository` AS `repo`,
			`Repository_URL` AS `repo_url`,
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
		ORDER BY `Created` ASC
	")->bind([
		'state' => 'open'
	])->execute()->get_results();

	$new_issue = "<a href=\"https://github.com/{$github->repository->full_name}/issues/new\" target=\"_blank\" title=\"New\" role=\"button\" data-icon=\"+\"></a>";
?>
<dialog id="<?=$file?>_dialog">
	<button type="button" data-delete="#<?=$file?>_dialog"></button><br />
	<table border="1">
		<caption>
			<?=count($issues);?> Open Issues on <a href="<?=$github->repository->html_url;?>" target="_blank"><?=$github->repository->name;?></a> <?=$new_issue;?>
		</caption>
		<thead>
			<tr>
				<th>Number</th>
				<th>Repo</th>
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
				<th>Repo</th>
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
					<a href="<?=$issue->repo_url?>" target="_blank"><?=end(explode('/', $issue->repo));?></a>
				</td>
				<td>
					<details>
						<summary><?=utf($issue->Title);?></summary>
						<kbd><?=nl2br(utf($issue->Body));?></kbd>
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
					<?php if(!empty($issue->Milestone)):?><a href="<?=$issue->Milestone_URL;?>" target="_blank"><?=$issue->Milestone;?></a><?php endif;?>
				</td>
			</tr>
		<?php endforeach;?>
		</tbody>
	</table>
</dialog>
