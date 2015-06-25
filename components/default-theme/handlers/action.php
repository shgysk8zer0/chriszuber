<?php
$resp = \shgysk8zer0\Core\JSON_Response::load();
switch($_POST['action']) {
	case 'logout':
		$login->logout();
		$session->destroy();
		$session = new \shgysk8zer0\Core\Session();
		nonce();

		$resp->enable(
			'#main_menu menuitem[label=Login]'
		)->disable(
			'#main_menu menuitem[label=Logout]'
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
		break;

	case 'Clear PHP_errors':
		require_login('admin');

		$DB->resetTable('errors');
		file_put_contents(BASE . '/errors.log', null, LOCK_EX);
		$resp->notify(
			'Success!',
			"Table (PHP_errors) has been reset",
			'images/icons/db.png'
		);
		break;

	case 'restore database':
		require_login('admin');

		$connect = \shgysk8zer0\Core\resources\Parser::parseFile('connect.json');
		($DB->restore($connect->database)) ? $resp->notify(
			'Success',
			"The database has been restored from {$connect->database}.sql",
			'images/icons/db.png'
		)->reload() : $resp->notify(
			'Failed',
			"There was a problem restoring from {$connect->database}.sql",
			'images/icons/db.png'
		);
		break;

	case 'backup database':
		require_login('admin');

		$connect = \shgysk8zer0\Core\resources\Parser::parseFile('connect.json');
		($DB->dump()) ? $resp->notify(
			'Success',
			"The database has been backed up to {$connect->database}.sql",
			'images/icons/db.png'
		) : $resp->notify(
			"Unable to backup to {$connect->database}.sql",
			'Check file permissions',
			'images/icons/db.png'
		);
		break;

	case 'update_sitemap':
		require_login('admin');

		update_sitemap();
		$resp->notify(
			'Sitemap has been updated',
			'View ' . URL . 'sitemap.xml',
			'images/icons/db.png'
		);
		break;

	case 'update_rss':
		require_login('admin');

		update_rss();
		$resp->notify(
			'Rss Feed has been updated',
			'View ' . URL . 'feed.rss',
			'images/icons/db.png'
		);
		break;

	case 'keep-alive':
		$resp->log('Kept-alive @ ' . date('h:i A'));
		break;

	case 'tracking_header_check':
		$headers = getallheaders();
		if (https()) {
			$resp->notify(
				'Your connection is encrypted',
				'The tracking header is only injected for non-encrypted traffic'
			);
		} elseif(array_key_exists('X-UIDH', $headers)) {
			$resp->notify(
				'Your carrier is tracking you!',
				'Your tracking ID is ' . $headers['X-UIDH']
			);
		} else {
			$resp->notify(
				'No tracking headers found.',
				'This only tests for one specific header, and does not mean that another doesn\'t exist'
			);
		}
		break;

	case 'git_command':
		require_login('admin');
		if (array_key_exists('prompt_value', $_POST) and strlen($_POST['prompt_value'])) {
			$command = 'git ' . escapeshellcmd($_POST['prompt_value']);
			$result = `{$command}`;
			$resp->notify(
				$command,
				$result,
				'images/logos/git.png'
			);
		}
		break;

	case 'recent_commits':
		$resp->remove(
			'#recent_commits_dialog, .backdrop'
		)->append(
			'body',
			load_results('recent_commits')
		)->showModal(
			'#recent_commits_dialog'
		);
		break;

	case 'github_issues':
		$resp->append(
			'body',
			load_results('github_issues')
		)->showModal(
			'#github_issues_dialog'
		);
		break;

	case 'README':
		$readme = new \shgysk8zer0\Core\File('README.md');
		$parser = new \Parsedown\Parsedown();
		$dialog = new \shgysk8zer0\Core\Elements\Dialog('README', $parser->text($readme));
		$resp->append('body', $dialog)->showModal($dialog->id);
		break;

	case 'update_icons':
		require_login('admin');
		$icons = \shgysk8zer0\Core\resources\Parser::parseFile('icons.json');
		$found = array_filter(array_unique($icons->icons), 'file_exists');
		if (count($found) === count($icons->icons)) {
			if (SVG_symbols($icons->icons, $icons->output)) {
				$resp->notify(
					'Success!',
					"Icons have been saved to {$icons->output}"
				);
			} else {
				$resp->notify(
					'We have a problem :(',
					'Was unable to update combined SVG due to an error parsing or saving the file',
					'images/octicons/svg/bug.svg'
				);
			}

		} else {
			$resp->notify(
				'We have a problem :(',
				'Some icons are missing. Check your browser\'s log',
				'images/octicons/svg/bug.svg'
			)->log([
				'Icons' => $icons,
				'Found' => $found,
				'Missing' => array_diff($icons->icons, $found)
			]);
		}
		break;

	case 'concatenate_js':
		require_login('admin');
		$scripts = array_map('current', get_dev_scripts());
		if (concatenate_scripts($scripts)) {
			$resp->notify(
				'Success!',
				'JavaScript files were concatenated'
			);
		} else {
			$resp->notify(
				'We have a problem :(',
				'JavaScript files were not concatenated',
				'images/octicons/svg/bug.svg'
			);
		}
		break;

	case 'test':
		require_login('admin');

		$resp->notify(
			'Edit Me',
			'I am on line ' . __LINE__ . ' in ' . __FILE__
		);
		break;
}

exit($resp);
