<?php
	error_reporting(0);
	header('Content-Type: text/plain');
	require_once __DIR__ . DIRECTORY_SEPARATOR . 'functions.php';

	$webhook = new \shgysk8zer0\core\GitHubWebhook('config/github.json');
	try {
		if($webhook->validate()) {
			$PDO = new \shgysk8zer0\core\PDO($webhook->config);
			switch(trim(strtolower($webhook->event))) {
				case 'push': {
					if($PDO->connected) {
						$PDO->prepare("
							INSERT INTO `Commits` (
								`SHA`,
								`Branch`,
								`Repository_Name`,
								`Repository_URL`,
								`Commit_URL`,
								`Commit_Message`,
								`Author_Name`,
								`Author_Username`,
								`Author_Email`,
								`Modified`,
								`Added`,
								`Removed`,
								`Time`
							) VALUES (
								:SHA,
								:Branch,
								:Repository_Name,
								:Repository_URL,
								:Commit_URL,
								:Commit_Message,
								:Author_Name,
								:Author_Username,
								:Author_Email,
								:Modified,
								:Added,
								:Removed,
								:Time
							);
						");

						$successes = array_filter($webhook->parsed->commits, function($commit) use (&$PDO, $webhook) {
							return $PDO->bind([
								'SHA' => $commit->id,
								'Branch' => $webhook->parsed->ref,
								'Repository_Name' => $webhook->parsed->repository->full_name,
								'Repository_URL' => $webhook->parsed->repository->html_url,
								'Commit_URL' => $commit->url,
								'Commit_Message' => $commit->message,
								'Author_Name' => $commit->author->name,
								'Author_Username' => $commit->author->username,
								'Author_Email' => $commit->author->email,
								'Modified' => join(', ', $commit->modified),
								'Added' => join(', ', $commit->added),
								'Removed' => join(', ', $commit->removed),
								'Time' => date('Y-m-d H:i:s', strtotime($commit->timestamp))
							])->execute();
						});
						exit('Imported' . count($successes) . ' of ' . count($webhook->parsed->commits));
					}

					else {
						throw new \Exception('Failed to connect to database', 500);
					}
				} break;

				case 'issues': {
					if($PDO->connected) {
						$PDO->prepare("INSERT INTO `Issues` (
								`Number`,
								`Repository`,
								`Repository_URL`,
								`Title`,
								`Body`,
								`URL`,
								`Labels`,
								`Assignee`,
								`Avatar`,
								`State`,
								`Milestone`,
								`Milestone_URL`,
								`Created_At`,
								`Updated_At`,
								`Closed_At`
							) VALUES (
								:Number,
								:Repository,
								:Repository_URL,
								:Title,
								:Body,
								:URL,
								:Labels,
								:Assignee,
								:Avatar,
								:State,
								:Milestone,
								:Milestone_URL,
								:Created_At,
								:Updated_At,
								:Closed_At
							) ON DUPLICATE KEY UPDATE
								`Title` = :Title,
								`Body` = :Body,
								`Labels` = :Labels,
								`Assignee` = :Assignee,
								`State` = :State,
								`Milestone` = :Milestone,
								`Updated_At` = :Updated_At,
								`Closed_At` = :Closed_At;
						")->bind([
							'Number' => $webhook->parsed->issue->number,
							'Repository' => $webhook->parsed->repository->full_name,
							'Repository_URL' => $webhook->parsed->repository->html_url,
							'Title' => $webhook->parsed->issue->title,
							'Body' => $webhook->parsed->issue->body,
							'URL' => $webhook->parsed->issue->html_url,
							'Labels' => join(', ', array_map(function($label) {
								return trim($label->name);
							}, $webhook->parsed->issue->labels)),
							'Assignee' => $webhook->parsed->issue->assignee->login,
							'Avatar' => $webhook->parsed->issue->assignee->avatar_url,
							'State' => $webhook->parsed->issue->state,
							'Milestone' => $webhook->parsed->issue->milestone->title,
							'Milestone_URL' => $webhook->parsed->issue->milestone->url,
							'Created_At' => date('Y-m-d H:i:s', strtotime($webhook->parsed->issue->created_at)),
							'Updated_At' => date('Y-m-d H:i:s', strtotime($webhook->parsed->issue->updated_at)),
							'Closed_At' => date('Y-m-d H:i:s', strtotime($webhook->parsed->issue->closed_at))
						]);

						if(!$PDO->execute()) {
							throw new \Exception('Failed inserting issue to database', 500);
						}
					}

					else {
						throw new \Exception('Failed to connect to database', 500);
					}
				} break;

				default: {
					file_put_contents($webhook->event . '_' . date('Y-m-d\TH:i:s') . '.json', json_encode($webhook->parsed, JSON_PRETTY_PRINT));
					throw new \Exception("Unhandled event: {$webhook->event}", 501);
				};
			}
		}
		else {
			throw new \Exception('Authorization required', 401);
		}
	}
	catch(\Exception $e) {
		http_response_code($e->getCode());
		exit("{$_SERVER['SERVER_NAME']} responded with: {$e->getMessage()} on line {$e->getLine()} in {$e->getFile()}");
	}
?>
