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
				if(array_keys_exist('title', 'description', 'keywords', 'author', 'content', $_POST)) {
					check_nonce();

					$title = urldecode(preg_replace('/' . preg_quote('<br>', '/') . '/', null, trim($_POST['title'])));
					$description = trim($_POST['description']);
					$keywords = urldecode(preg_replace('/' . preg_quote('<br>', '/') . '/', null, trim($_POST['keywords'])));
					$author = trim($_POST['author']);
					$content = urldecode(trim($_POST['content']));
					$url = urlencode(strtolower(preg_replace('/\W/', null, $title)));

					$template = template::load('blog');
					$time = new simple_date();
					$template->set([
						'title' => $title,
						'tags' => $keywords,
						'content' => $content,
						'author' => $author,
						'author_url' => $url,
						'date' => $time->out('m/d/Y'),
						'datetime' => $time->out()
					]);
					ob_start();
					$template->out();
					$post = ob_get_clean();
					$resp->html(
						'main',
						$post
					);

					$DB->prepare("
						INSERT INTO `posts`(
							`title`,
							`description`,
							`keywords`,
							`author`,
							`content`,
							`url`
						) VALUE(
							:title,
							:description,
							:keywords,
							:author,
							:content,
							:url
						)
					")->bind([
						'title' => $title,
						'description' => $description,
						'keywords' => $keywords,
						'author' => $author,
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

	else http_status(404);

	$resp->send();
	exit();
?>
