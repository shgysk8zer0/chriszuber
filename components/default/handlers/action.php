<?php
	$resp = \core\json_response::load();
	switch($_POST['action']) {
		case 'logout': {
			$login->logout();
			$session->destroy();
			$session = new \core\session();
			nonce();

			$resp->enable(
				'menu[label=Account] menuitem[label=Login]'
			)->disable(
				'menu[label=Account] menuitem[label=Logout]'
			)->attributes(
				'body > main',
				'contextmenu',
				false
			)->sessionStorage(
				'nonce',
				$session->nonce
			)->notify(
				'User has been logged out',
				'Login again to make changes.',
				'images/icons/people.png'
			);
		}break;

		case 'Clear PHP_errors': {
			require_login('admin');
			$pdo =\core\PDO::load('connect');

			$pdo->reset_table('PHP_errors');
			file_put_contents(BASE . '/errors.log', null, LOCK_EX);
			$resp->notify(
				'Success!',
				"Table (PHP_errors) has been reset",
				'images/icons/db.png'
			)->remove(
				'main > *'
			);
		} break;

		case 'restore database': {
			require_login('admin');

			$connect = \core\ini::load('connect');
			($DB->restore($connect->database)) ? $resp->notify(
				'Success',
				"The database has been restored from {$connect->database}.sql",
				'images/icons/db.png'
			)->reload() : $resp->notify(
				'Failed',
				"There was a problem restoring from {$connect->database}.sql",
				'images/icons/db.png'
			);
		} break;

		case 'backup database': {
			require_login('admin');

			$connect = \core\ini::load('connect');
			($DB->dump()) ? $resp->notify(
				'Success',
				"The database has been backed up to {$connect->database}.sql",
				'images/icons/db.png'
			) : $resp->notify(
				"Unable to backup to {$connect->database}.sql",
				'Check file permissions',
				'images/icons/db.png'
			);
		} break;

		case 'update_sitemap': {
			require_login('admin');

			update_sitemap();
			$resp->notify(
				'Sitemap has been updated',
				'View ' . URL . '/sitemap.xml',
				'images/icons/db.png'
			);
		} break;

		case 'update_rss': {
			require_login('admin');

			update_rss();
			$resp->notify(
				'Rss Feed has been updated',
				'View ' . URL . '/feed.rss',
				'images/icons/db.png'
			);
		} break;

		case 'keep-alive': {
			$resp->log('Kept-alive @ ' . date('h:i A'));
		} break;

		case 'tracking_header_check': {
			$headers = getallheaders();
			if(https()) {
				$resp->notify(
					'Your connection is encrypted',
					'The tracking header is only injected for non-encrypted traffic'
				);
			}
			elseif(array_key_exists('X-UIDH', $headers)) {
				$resp->notify(
					'Your carrier is tracking you!',
					'Your tracking ID is ' . $headers['X-UIDH']
				);
			}

			else {
				$resp->notify(
					'No tracking headers found.',
					'This only tests for one specific header, and does not mean that another doesn\'t exist'
				);
			}
		} break;

		case 'git_command': {
			require_login('admin');
			if(array_key_exists('prompt_value', $_POST) and strlen($_POST['prompt_value'])) {
				$command = 'git ' . escapeshellcmd($_POST['prompt_value']);
				$result = `{$command}`;
				$resp->notify(
					$command,
					$result,
					'images/logos/git.png'
				);
			}
		} break;

		case 'recent_commits': {
			$resp->remove(
				'#recent_commits_dialog, .backdrop'
			)->append(
				'body',
				load_results('recent_commits')
			)->showModal(
				'#recent_commits_dialog'
			);
		} break;

		case 'github_issues': {
			$resp->append(
				'body',
				load_results('github_issues')
			)->showModal(
				'#github_issues_dialog'
			);
		} break;
		case 'test': {
			require_login('admin');

			$resp->notify(
				'Edit Me',
				'I am on line ' . __LINE__ . ' in ' . __FILE__
			)->table($DB->fetch_array("
				SELECT
					`line` AS `Line #`,
					`file` AS `File`,
					`error_type` AS `Type`,
					`error_message` AS `Message`,
					`datetime` AS `Timestamp`
				FROM `PHP_errors`
				GROUP BY `file`, `line`
				ORDER BY `Timestamp` DESC
			"));
		}break;
	}
?>
