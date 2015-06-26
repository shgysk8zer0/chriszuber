<?php
namespace shgysk8zer0\Core;
error_reporting(0);
define('BASE', __DIR__);
init();
$timer   = new Timer;
$date    = new DateTime;
$headers = new Headers;
$URL     = new URL;
$API     = new API;
$PDO     = new PDO;
$login   = new Login;

try {
	if (isset($headers->user, $headers->password)) {
		$login->loginWith(['user' => $headers->user, 'password' => $headers->password]);
	} elseif (isset($API->request->user, $API->request->password)) {
		$login->loginWith(['user' => $API->request->user, 'password' => $API->request->password]);
	} elseif (isset($URL->user, $URL->pass)) {
		$login->loginWIth(['user' => $URL->user, 'password' => $URL->pass]);
	}
	unset(
		$headers->server,
		$headers->x_powered_by,
		$headers->cache_control,
		$headers->expires,
		$headers->x_frame_options,
		$URL->user,
		$URL->pass
	);
	$API->request_time = "$date";
	$API->request_url = "$URL";
	if (isset($API->request->action)) {
		switch($API->request->action) {
			case 'get_posts':
				if (isset($API->request->title)) {
					$API->post = $PDO->prepare(
						'SELECT *
						FROM `posts`
						WHERE `title` = :title
						LIMIT 1;'
					)->execute([
						'title' => $API->request->title
					])->getResults(0);
				} elseif (isset($API->request->author)) {
					$API->posts = $PDO->prepare(
						'SELECT `title`, `description`, `keywords`, `created`
						FROM `posts`
						WHERE `author` = :author;'
					)->execute([
						'author' => $API->request->author
					])->getResults();
				} elseif (isset($API->request->count)) {
					$API->posts = $PDO('SELECT COUNT(*) as `count` FROM `posts`');
				} else {
					throw new Exceptions\HTTP('Request made for get_posts, but no search criteria given', 404);
				}
				break;

			case 'status':
				if ($login->logged_in and $login->role === 'admin') {
					$API->memory =  memory_get_usage(true) / 1024 . ' MB';
					$API->load = array_combine(
						array('1 minute', '5 minutes', '15 minutes'),
						sys_getloadavg()
					);
					$API->loaded_files = get_included_files();
				} else {
					throw new Exceptions\HTTP('Login is required to retrieve system info', 401);
				}

				break;

			case 'view_file':
				if (! ($login->logged_in and $login->role === 'admin')) {
					throw new Exceptions\HTTP('Operation requires admin privelages', 401);
				} elseif (@is_string($API->request->file) and ! empty($API->request->file)) {
					if (file_exists($API->request->file)) {
						$API->{$API->request->file} = file_get_contents($API->request->file);
					} else {
						throw new Exceptions\HTTP(sprintf('File [%s] could not be found', $API->request->file), 404);
					}
				} else {
					throw new Exceptions\HTTP('No file requested', 412);
				}
				break;

			case 'get_files':
				if (! ($login->logged_in and $login->role === 'admin')) {
					throw new Exceptions\HTTP('Operation requires admin privelages', 401);
				} elseif (!@is_array($API->request->files_list)) {
					throw new Exception\HTTP('Expected an array in "files_list"', 412);
				} else {
					$API->request->files_list = array_filter($API->request->files_list, 'file_exists');
					$API->files = array_map('file_get_contents', $API->request->files_list);
					$API->files = array_combine($API->request->files_list, $API->files);
				}
				break;

			case 'git_status':
				$API->git_status = `git status`;
				break;

			default:
				throw new \Exception('Invalid action requested', 500);
		}
	} else {
		throw new Exceptions\HTTP('Action is a required paramater', 406);
	}
} catch (Exceptions\HTTP $e) {
	http_response_code($e->getCode());
	$API->exception = $e->getMessage();
} catch (\Exception $e) {
	http_response_code(500);
	exit;
}
$API->logged_in = $login->logged_in ? 'true' : 'false';
$API->time_spent = "$timer";
exit($API);
