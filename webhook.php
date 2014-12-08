<?php
	error_reporting(0);
	header('Content-Type: text/plain');
	require_once __DIR__ . DIRECTORY_SEPARATOR . 'functions.php';

	$webhook = new \core\GitHubWebhook('config/github.json');
	try {
		if($webhook->validate()) {
			switch(trim(strtolower($webhook->event))) {
				case 'push': {
					$PDO = new \core\PDO($webhook->config->database);
					if($PDO->connected) {
						$PDO->prepare("
							INSERT INTO `Commits` (
								`SHA`,
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
					$PDO = new \core\PDO($webhook->config->database);
					if($PDO->connected) {
						$PDO->prepare("
							INSERT INTO `Issues` (
								`Number`,
								`Title`,
								`URL`,
								`Labels`,
								`State`,
								`Milestone`,
								`Created_At`,
								`Closed_At`,
								`Body`,
								`Repository`,
								`Repository_URL`
							) VALUES (
								:Number,
								:Title,
								:URL,
								:Labels,
								:State,
								:Milestone,
								:Created_At,
								:Closed_At,
								:Body,
								:Repository,
								:Repository_URL
							)
							ON DUPLICATE KEY UPDATE
								`Title` = :Title,
								`Labels` = :Labels,
								`State` = :State,
								`Milestone` = :Milestone,
								`Closed_At` = :Closed_At,
								`Body` = :Body
							;
						")->bind([
							'Number' => $webhook->parsed->issue->number,
							'Title' => $webhook->parsed->issue->title,
							'URL' => $webhook->parsed->issue->html_url,
							'Labels' => join(', ', array_map(function($label){
								return trim($label->name);
							}, $webhook->parsed->issue->labels)),
							'State' => $webhook->parsed->issue->state,
							'Milestone' => $webhook->parsed->issue->milestone,
							'Created_At' => date('Y-m-d H:i:s', strtotime($webhook->parsed->issue->created_at)),
							'Closed_At' => date('Y-m-d H:i:s', strtotime($webhook->parsed->issue->closed_at)),
							'Body' => $webhook->parsed->issue->body,
							'Repository' => $webhook->parsed->repository->full_name,
							'Repository_URL' => $webhook->parsed->repository->html_url
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
