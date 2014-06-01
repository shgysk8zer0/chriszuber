<?php
	$session = session::load();
	$login = login::load();
	$connect = ini::load('connect');
	$resp = new json_response();

	if(array_key_exists('load', $_POST)){
		switch($_POST['load']) {
			default:
				$resp->html(
					'main',
					load_results($_POST['load'])
				);
		}
	}

	elseif(array_key_exists('load_form', $_POST)) {
		switch($_POST['load_form']) {
			case 'login':
				$resp->html(
					'main',
					load_results('forms/login')
				);
				break;
			case 'new_post':
				require_login();
				$resp->html(
					'main',
					load_results('forms/new_post')
				);
			break;
		}
	}

	elseif(array_key_exists('form', $_POST)) {
		switch($_POST['form']) {
			case 'login':
				if(array_keys_exist('user', 'password', $_POST)) {
					check_nonce();
					$login->login_with($_POST);
					if($login->logged_in) {
						$session->setUser($login->user)->setPassword($login->password)->setRole($login->role)->setLogged_In(true);
						$resp->setAttributes([
							'menu[label=Account] menuitem:not([label=Logout])' => [
								'disabled' => true
							],
							'menuitem[label=Logout]' => [
								'disabled' => false
							],
							'body > main' => [
								'contextmenu' => false,
								'data-menu' => 'admin'
							]
						])->remove(
							'main > *'
						)->notify(
							'Login successful',
							"Welcome back {$login->user}",
							'images/icons/people.png'
						);
					}
					else {
						$resp->notify(
							'Login not accepted',
							'Check your email & password',
							'images/icons/people.png'
						);
					}
				}
				else {
					$resp->notify(
						'Login not accepted',
						'Check your email & password',
						'images/icons/people.png'
					);
				}
				break;

			case 'new_post':
				check_nonce();
				require_login('admin');
				if(array_keys_exist('title', 'description', 'keywords', 'content', $_POST)) {

					$user = $DB->prepare('
						SELECT `g_plus`, `name`
						FROM `users`
						WHERE `user` = :user
						LIMIT 1
					')->bind([
						'user' => $login->user
					])->execute()->get_results(0);

					$title = urldecode(preg_replace('/' . preg_quote('<br>', '/') . '/', null, trim($_POST['title'])));
					$description = trim($_POST['description']);
					$keywords = urldecode(preg_replace('/' . preg_quote('<br>', '/') . '/', null, trim($_POST['keywords'])));
					$author = $user->name;
					$content = urldecode(trim($_POST['content']));
					$url = urlencode(strtolower(preg_replace('/\W/', null, $title)));

					$tags = [];
					foreach(explode(',', $keywords) as $tag) $tags[] = '<a href="tags/' . trim(strtolower(preg_replace('/\s/', '-', trim($tag)))) . '">' . trim(caps($tag)) . "</a>";

					$template = template::load('blog');
					$time = new simple_date();
					$template->set([
						'title' => $title,
						'tags' => join(PHP_EOL, $tags),
						'content' => $content,
						'author' => $user->name,
						'author_url' => $user->g_plus,
						'date' => $time->out('m/d/Y'),
						'datetime' => $time->out()
					]);
					ob_start();
					$template->out();
					$resp->html(
						'main',
						ob_get_clean()
					);

					$DB->prepare("
						INSERT INTO `posts`(
							`title`,
							`description`,
							`keywords`,
							`author`,
							`author_url`,
							`content`,
							`url`
						) VALUE(
							:title,
							:description,
							:keywords,
							:author,
							:author_url,
							:content,
							:url
						)
					")->bind([
						'title' => $title,
						'description' => $description,
						'keywords' => $keywords,
						'author' => $user->name,
						'author_url' => $user->g_plus,
						'content' => $content,
						'url' => $url
					]);
					($DB->execute()) ? $resp->notify(
						'Post submitted',
						'Check for new posts'
					)->remove(
						'main > *'
					) : $resp->notify(
						'Post failed',
						'Look into what went wrong'
					);
				}
				else {
					$resp->notify(
						'Something went wrong...',
						'There seems to be some missing info.'
					);
				}
				break;
		}
	}

	elseif(array_key_exists('load_menu', $_POST)) {
		switch($_POST['load_menu']) {
			default:
				$resp->prepend(
					'body',
					load_results("menus/{$_POST['load_menu']}")
				);
		}
	}

	elseif(array_key_exists('action', $_POST)) {
		switch($_POST['action']) {
			case 'logout':
				$login->logout();
				$session->destroy();
				$session = new session($connect->site);
				nonce();
				$resp->setAttributes([
					'menu[label=Account] menuitem[label=Login]' => [
						'disabled' => false
					],
					'menu[label=Account] menuitem[label=Logout]' => [
						'disabled' => true
					],
					'body > main' => [
						'contextmenu' => false
					]
				])->remove(
					'main > *'
				)->sessionStorage(
					'nonce',
					$session->nonce
				)->notify(
					'User has been logged out',
					'Login again to make changes.',
					'images/icons/people.png'
				);
				break;
		}
	}

	elseif(array_key_exists('request', $_POST)) {
		switch($_POST['request']) {
			case 'nonce': {
				$resp->sessionStorage(
					'nonce',
					$session->nonce
				);
			}
		}
	}

	$resp->send();
	exit();
?>
