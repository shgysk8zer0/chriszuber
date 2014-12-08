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

				default: {
					file_put_contents($webhook->event . '_' . date('Y-m-d\TH:i:s') . '.json', json_encode($webhook->parsed, true));
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
