<?php
	//require_once('./custom.php');
	$session = session::load();
	$login = login::load();
	$connect = ini::load('connect');
	$resp = new json_response();

	if(!count($_REQUEST)) {
		$page = pages::load();
		$resp->remove(
			'main > :not(aside)'
		)->prepend(
			'main',
			$page->content
		);

		if($page->type === 'posts') {
			$resp->attributes(
				'meta[name=description], meta[itemprop=description]',
				'content',
				$page->description
			)->attributes(
				'meta[name=keywords], meta[itemprop=keywords]',
				'content',
				$page->keywords
			)->attributes(
				'meta[name=author], meta[itemprop=author]',
				'content',
				$page->author
			);
		}
	}

	elseif(array_key_exists('load', $_POST)){
		switch($_POST['load']) {
			default:
				/*$resp->html(
					'main',
					load_results($_POST['load'])
				);*/
			$resp->notify(
				'The basic load method is depreciated',
				'Please update to a more specific request'
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
		switch(trim($_POST['form'])) {
			case 'login': {
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
								'contextmenu' => 'admin_menu'
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
			}break;

			case 'tag_search': {
				if(array_key_exists('tags', $_POST)) {
					$posts = $DB->prepare("
						SELECT `title`, `description`, `author`, `author_url`, `url`, `created`
						FROM `posts`
						WHERE `keywords` LIKE :tag
						LIMIT 20
					")->bind([
						'tag' => "%{$_POST['tags']}%"
					])->execute()->get_results();

					if($posts) {
						$content = '<div class="tags">';

						$template = template::load('tags');

						foreach($posts as $post) {
							$datetime = new simple_date($post->created);
							$content .= $template->set([
								'title' => $post->title,
								'description' => $post->description,
								'author' => $post->author,
								'author_url' => $post->author_url,
								'url' => ($post->url === '')? URL : URL .'/posts/' . $post->url,
								'date' => $datetime->out('D M jS, Y \a\t h:iA')
							])->out();
						}
						$content .= '</div>';

						$resp->remove(
							'main > :not(aside)'
						)->prepend(
							'main',
							$content
						);
					}
				}
				else {
					$resp->notify(
						'Error',
						'tags not set'
					);
				}
			} break;

			case 'new_post': {
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
					$url = urlencode(strtolower(preg_replace('/\W+/', ' ', $title)));

					$tags = [];
					foreach(explode(',', $keywords) as $tag) $tags[] = '<a href="tags/' . trim(strtolower(preg_replace('/\s/', '-', trim($tag)))) . '">' . trim(caps($tag)) . "</a>";

					$template = template::load('posts');
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
			}break;

			case 'edit_post': {
				check_nonce();
				require_login('admin');

				if(array_keys_exist('title', 'keywords', 'content', 'old_title', 'description', $_POST)) {
					$DB->prepare("
						UPDATE `posts`
						SET `title` = :title,
						`keywords` = :keywords,
						`description` = :description,
						`content` = :content
						WHERE `title` = :old_title
						LIMIT 1
					")->bind([
						'title' => urldecode(preg_replace('/' . preg_quote('<br>', '/') . '/', null, trim($_POST['title']))),
						'keywords' => urldecode(preg_replace('/' . preg_quote('<br>', '/') . '/', null, trim($_POST['keywords']))),
						'description' => trim($_POST['description']),
						'content' => urldecode(trim($_POST['content'])),
						'old_title' => urldecode(trim($_POST['old_title']))
					]);
					($DB->execute()) ? $resp->notify(
						"Post has been updated.",
						"{$_POST['old_title']} has been updated.",
						'images/icons/db.png'
					) : $resp->notify(
						'Something went wrong :(',
						"There was a problem updating {$_POST['old_title']}",
						'images/icons/db.png'
					);
				}
				else {
					$resp->notify(
						'Error Updating Post',
						'It seems some info was missing',
						'images/icons/db.png'
					);
				}
			}
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

	elseif(array_key_exists('datalist', $_REQUEST)) {
		$resp->prepend(
			"[list=\"{$_REQUEST['datalist']}\"]",
			get_datalist($_REQUEST['datalist'])
		);
	}

	elseif(array_key_exists('template', $_REQUEST)) {
		switch($_REQUEST['template']) {
			default: {
				$template = get_template($_REQUEST['template']);
			}
		}

		if($template) $resp->template = preg_replace('/\%.+\%/', null, $template);
	}

	elseif(array_key_exists('action', $_POST)) {
		switch($_POST['action']) {
			case 'logout': {
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
			}break;

			case 'Clear PHP_errors': {
				$pdo = _pdo::load();
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
				check_nonce();
				require_login('admin');

				($DB->restore($connect->database)) ? $resp->notify(
					'Success',
					"The database has been restored from {$connect->database}.sql",
					'images/icons/db.png'
				) : $resp->notify(
					'Failed',
					"There was a problem restoring from {$connect->database}.sql",
					'images/icons/db.png'
				);
			} break;

			case 'backup database': {
				check_nonce();
				require_login('admin');

				$DB->dump();
				$resp->notify(
					'Success',
					"The database has been backed up to {$connect->database}.sql",
					'images/icons/db.png'
				);
			} break;

			case 'test': {
				$resp->notify(
					'Edit Me',
					'I am on line ' . __LINE__ . ' in ' . __FILE__
				);
			}break;
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

	elseif(array_key_exists('debug', $_POST)) { // Debugging only available to admins
		check_nonce();
		require_login('admin');

		ob_start();
		switch(trim($_POST['debug'])) {

			case 'headers': {
				debug(headers_list());
			}break;

			case 'extensions': {
				debug(get_loaded_extensions());
			}break;

			case 'modules': {
				debug(apache_get_modules());
			}break;

			default: {
				debug($$_POST['debug']);
			}
		}
		$resp->html(
			'body > main',
			ob_get_clean()
		);
	}

	/*else {
		ob_start();
		debug($_REQUEST);
		debug($_SERVER);

		$resp->html('main', ob_get_clean());
	}*/

	$resp->send();
	exit();
?>
