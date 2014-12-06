<?php
	error_reporting(0);
	set_include_path(__DIR__ . DIRECTORY_SEPARATOR . 'classes' . PATH_SEPARATOR . get_include_path());
	spl_autoload_extensions('.class.php');
	spl_autoload_register();
	header('Content-Type: text/plain');

	$webhook = new \core\GitHubWebhook('config/github.json');
	try {
		if($webhook->validate()) {
			switch(trim(strtolower($webhook->event))) {
				case 'push': {
					$table = new \core\table('author', 'commit', 'message', 'time', 'modified', 'added', 'removed');
					$table->caption = "Recently pushed to <a href=\"{$webook->parsed->repository->html_url}\" target=\"_blank\">{$webook->parsed->repository->full_name}";
					array_map(function($commit) use (&$table) {
						$table->author(
							"<a href=\"mailto:{$commit->author->email}\" title=\"{$commit->author->name}\">{$commit->author->username}</a>"
						)->commit(
							"<a href=\"{$commit->url}\" target=\"_blank\">{$commit->id}</a>"
						)->message(
							$commit->message
						)->time(
							date('Y-m-d H:i:s', strtotime($commit->timestamp))
						)->modified(
							join(', ', $commit->modified)
						)->added(
							join(', ', $commit->added)
						)->removed(
							join(', ', $commit->removed)
						)->next_row();
					}, $webhook->parsed->commits);

					$email = new \core\email(
						$_SERVER['SERVER_ADMIN'],
						"Recently pushed to {$webook->parsed->repository->full_name}",
						$table->out(false, true)
					);
					if(!$email->send(true)) {
						throw new \Exception('Failed sending email', 500);
					}
				} break;

				default: {
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
