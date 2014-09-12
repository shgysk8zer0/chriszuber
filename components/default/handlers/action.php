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
			$pdo =\core\_pdo::load('connect');

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

		case 'test': {
			require_login('admin');

			$resp->notify(
				'Edit Me',
				'I am on line ' . __LINE__ . ' in ' . __FILE__
			);
		}break;
	}
?>
