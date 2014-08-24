<?php
	$session = session::load();
	$DB = _pdo::load('connect');
	$login = login::load();
	$connect = ini::load('connect');
	$resp = new json_response();

	if(!count($_REQUEST)) {
		$page = pages::load();
		$head = $DB->fetch_array("
			SELECT `value` FROM `head`
			WHERE `name` = 'title'
		", 0);

		$resp->remove(
			'main > :not(aside)'
		)->prepend(
			'main',
			$page->content
		)->scrollTo(
			'main :first-child'
		);

		$resp->attributes(
			'meta[name=description], meta[itemprop=description]',
			'content',
			$page->description
		)->attributes(
			'meta[name=keywords], meta[itemprop=keywords]',
			'content',
			$page->keywords
		)->text(
			'head > title',
			"{$page->title} | {$head->value}"
		);
	}

	elseif(array_key_exists('load_form', $_POST)) {
		switch($_POST['load_form']) {
			case 'login': {
				$resp->remove(
					'main > :not(aside)'
				)->prepend(
					'main',
					load_results('forms/login')
				);
				} break;

			case 'new_post': {
				require_login();
				$resp->remove(
					'main > :not(aside)'
				)->prepend(
					'main',
					load_results('forms/new_post')
				);
			} break;

			case 'php_errors': {
				require_login('admin');

				$resp->html(
					'main',
					load_results("forms/php_errors")
				);
			} break;
		}
	}

	elseif(array_key_exists('form', $_POST)) {
		check_nonce();
		switch(trim($_POST['form'])) {
			case 'login': {
				if(array_keys_exist('user', 'password', $_POST)) {
					$login->login_with($_POST);

					if($login->logged_in) {
						$session->setUser($login->user)->setPassword($login->password)->setRole($login->role)->setLogged_In(true);

						$resp->close(
							'#loginDialog'
						)->disable(
							'menu[label=Account] menuitem:not([label=Logout])'
						)->enable(
							'menuitem[label=Logout]'
						)->attributes(
							'body > main',
							'contextmenu',
							'admin_menu'
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
							$content .= $template->title(
								$post->title
							)->description(
								$post->description
							)->author(
								$post->author
							)->author_url(
								$post->author_url
							)->url(
								($post->url === '')? URL : URL .'/posts/' . $post->url
							)->date(
								$datetime->out('D M jS, Y \a\t h:iA')
							)->out();
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

					$title = urldecode(trim(strip_tags($_POST['title'])));
					$description = trim($_POST['description']);
					$keywords = urldecode(trim(strip_tags($_POST['keywords'])));
					$author = $user->name;
					$content = trim($_POST['content']);
					$url = urlencode(strtolower(preg_replace('/\W+/', ' ', $title)));
					$time = new simple_date();

					foreach(explode(',', $keywords) as $tag) {
						$template->tags .= '<a href="tags/' . trim(strtolower(preg_replace('/\s/', '-', trim($tag)))) . '">' . trim(caps($tag)) . "</a>";
					}

					$DB->prepare("
						INSERT INTO `posts`(
							`title`,
							`description`,
							`keywords`,
							`author`,
							`author_url`,
							`content`,
							`url`,
							`created`
						) VALUE(
							:title,
							:description,
							:keywords,
							:author,
							:author_url,
							:content,
							:url,
							:created
						)
					")->bind([
						'title' => $title,
						'description' => $description,
						'keywords' => $keywords,
						'author' => $user->name,
						'author_url' => $user->g_plus,
						'content' => $content,
						'url' => $url,
						'created' => date('Y-m-d H:i:s')
					]);

					if($DB->execute()) {
						$template = template::load('posts');
						$template->title(
							$title
						)->content(
							$content
						)->author(
							$user->author
						)->author_url(
							$user->g_plus
						)->date(
							$time->out('m/d/Y')
						)->datetime(
							$time->out()
						);
						$url = URL . '/posts/' . $url;
						$resp->notify(
							'Post submitted',
							'Check for new posts'
						)->remove(
							'main > :not(aside)'
						)->prepend(
							'body > header nav',
							"<a href=\"{$url}\">{$title}</a>"
						)->prepend(
							'main',
							$template->out()
						)->after(
							'#main_menu > menu[label="Posts"] > menuitem[label="Home"]',
							"<menuitem label=\"{$title}\" icon=\"images/icons/coffee.svgz\" data-link=\"{$url}\"></menuitem>"
						);
						$resp->remove(
							'main > :not(aside)'
						);
							update_sitemap();
							update_rss();
						}
					else {
						$resp->notify(
							'Post failed',
							'Look into what went wrong. See Developer console'
						)->log($_POST);
					}
				}
				else {
					$resp->notify(
						'Something went wrong...',
						'There seems to be some missing info.'
					);
				}
			} break;

			case 'edit_post': {
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
						'content' => trim($_POST['content']),
						'old_title' => urldecode(trim($_POST['old_title']))
					]);
					if($DB->execute()) {
						$resp->notify(
							"Post has been updated.",
							"{$_POST['old_title']} has been updated.",
							'images/icons/db.png'
						);
						update_sitemap();
						update_rss();
					}
					else {
						$resp->notify(
							'Something went wrong :(',
							"There was a problem updating {$_POST['old_title']}",
							'images/icons/db.png'
						);
					}
				}
				else {
					$resp->notify(
						'Error Updating Post',
						'It seems some info was missing',
						'images/icons/db.png'
					);
				}
			} break;

			case 'php_errors': {
				require_login('admin');

				/**
				 * First, check that the input for "file" is set and matches the pattern.
				 *
				 * We have already checked that the user is logged in as admin and
				 * has a valid session.
				 */

				if(array_key_exists('file', $_POST) and preg_match('/^[A-z\/]+\.php$/', $_POST['file'])) {
					/**
					 * Next, do a query for all errors in PHP_errors where
					 * the file is $_POST['file'] (Add on BASE . '/' to fill in
					 * the full value, since I trim these for the input but they
					 * are stored with their full paths)
					 *
					 * Sort them according to line to make easier to navigate.
					 */

					/**
					 * Create an array to map the HTML table's columns to the
					 * Database's table columns.
					 *
					 * Keys will be used for the <table> and values will be used for
					 * the database's columns.
					 *
					 * This way, it should be easy to modify without having to switch
					 * things around in varous places.
					 *
					 * We will also use the values of the array (columns
					 * in the database's table) to build the query. You can
					 * change $mapping and it changes everything else.
					 */

					$mapping = [
						'Error Type' => 'error_type',
						'Line #' => 'line',
						'Message' => 'error_message',
						'Date Time' => 'datetime'
					];

					$select = '`' . join('`, `', array_values($mapping)) . '`';

					if(array_key_exists('level', $_POST) and $_POST['level'] !== '*') {
						$DB->prepare("
							SELECT {$select}
							FROM `PHP_errors`
							WHERE `file` = :file
							AND `error_type` = :level
							ORDER BY `line`
						")->bind([
							'file' => BASE . '/' . $_POST['file'],
							'level' => $_POST['level']
						]);
					}
					else {
						$DB->prepare("
							SELECT {$select}
							FROM `PHP_errors`
							WHERE `file` = :file
							ORDER BY `line`
						")->bind([
							'file' => BASE . '/' . $_POST['file']
						]);;
					}
					$errors = $DB->execute()->get_results();

					/**
					 * Check if an errors were found (if no
					 * errors were found, it will be an empty
					 * array and count() will be 0 and evaluate
					 * as false.)
					 */

					if(is_array($errors) and count($errors)) {
						/**
						 * We will be building an HTML <table>,
						 * using headers and footers for column names,
						 * and the cells will be each value from the
						 * $errors array.
						 *
						 * Just setup the opening tag (head and foot also
						 * get the row starting tag)
						 */

						$table = '<table border="1">';
						$thead = '<thead><tr>';
						$tfoot = '<tfoot><tr>';
						$tbody = '<tbody>';

						$table .= '<caption>PHP Errors</caption>';

						/**
						 * Loop through the array's keys, setting up
						 * both <thead> and >tfoot> at the same time.
						 */

						foreach(array_keys($mapping) as $th) {
							$thead .= "<th>{$th}</th>";
						}

						$thead .= '</tr></thead>';
						$tfoot .= '</tr></tfoot>';

						/**
						 * Add both thead and tfoot to the table (
						 * table footers actually go before the
						 * table body)
						 */

						$table .= $thead;
						$table .= $tfoot;

						foreach($errors as $error) {
							/**
							 * Loop through errors, just setting up
							 * the table row for now. Actual values
							 * will be done in a bit.
							 */

							$tr = '<tr>';

							foreach(array_values($mapping) as $cell) {
								/**
								 * Now it's time to fill in the table.
								 * We already have all of the errors and
								 * are going through each error to add the
								 * cells.
								 *
								 * Since we have $mappping, which alrady has
								 * all of the database's column names, and
								 * each error's value is mapped to $error as
								 * $error->$column_name, we can re-use this array
								 * to always add the correct cells and in the
								 * correct order.
								 */

								$tr .= "<td>{$error->$cell}</td>";
							}

							/**
							 * Add the row's closing tag and add the entire
							 * <tr> to <tbody>
							 */

							$tr .= '</tr>';
							$tbody .= $tr;
						}

						/**
						 * Add the entire <tbody> to <table> and add the closing
						 * tag to <table>
						 */

						$table .= $tbody;

						$table .= '</table>';

						/**
						 * Set <main.'s innerHTML to be <table>
						 */

						$resp->html(
							'main',
							$table
						);
					}

					else {
						$resp->notify(
							'Nothing to report',
							"No errors in {$_POST['file']} of that type",
							'images/icons/db.png'
						);
					}
				}

				else {
					$resp->notify(
						'Something went wrong :(',
						'Either file is not set or does not match the set pattern',
						'images/icons/db.png'
					);
				}
			} break;

			case 'comments': {
				$invalid = find_invalid_inputs([
					'comment_author' => '[\w\- ]+',
					'comment_email' => '.+'
				]);

				if(is_null($invalid)) {
					$comment =str_replace(
						["\r", "\n", "\r\n"],
						['<br />'],
						strip_tags(
							preg_replace_callback(
								'/(?<=\<code\>).*?(?=\<\/code\>)/',
								function($code) {
									return htmlentities($code[0]);
								},
								$_POST['comment']
							),
							'<br><p><span><div><a><ul><ol><li><i><u><b><em><u><h1><h2><h3><h4><h5><h6><pre><s><samp><strong><big><small><sup><sub><del><ins><code><var><kbd><cite>'
						)
					);
					$post = $_POST['for_post'];
					$template = template::load('comments');
					$author = $_POST['comment_author'];
					$author_url = (array_key_exists('comment_url', $_POST)) ? $_POST['comment_url'] : '';
					$author_email = $_POST['comment_email'];
					$time = date('Y-m-d H:i:s');

					$DB->prepare("
						INSERT INTO `comments`(
							`comment`,
							`author`,
							`author_url`,
							`author_email`,
							`post`
						) VALUES (
							:comment,
							:author,
							:author_url,
							:author_email,
							:post
						)
					")->bind([
						'comment' => $comment,
						'author' => $author,
						'author_url' => $author_url,
						'author_email' => $author_email,
						'post' => $post
					])->execute();

					$resp->close(
						'#new_comment'
					)->append(
						'#comments_section',
						$template->comment(
							$comment
						)->time(
							date('l, F jS Y h:i A')
						)->author(
							$author
						)->out()
					)->notify(
						'Comment Submitted',
						"Your comment has been added to “{$_POST['post_title']}”"
					)->clear(
						'comments'
					)->scrollTo(
						'[itemtype="http://schema.org/UserComments"]:last-of-type'
					)->log(
						$_POST
					);
				}
			} break;

			case 'install': {
				if($DB->connected) {
					$resp->notify(
						'No need to install...',
						'It is already done!'
					);
				}
				else {
					if(array_key_exists('install', $_POST) and is_array($_POST['install']) and array_keys_exist('root', 'head', 'site', $_POST['install'])) {
						$created_con = false;
						$root = (object)$_POST['install']['root'];
						$root->database = 'information_schema';
						$head = (object)$_POST['install']['head'];
						$head->viewport = 'width=device-width, height=device-height';
						$head->charset = 'UTF-8';
						$site = (object)$_POST['install']['site'];
						if(
							array_key_exists('connect', $_POST['install'])
							and is_array($_POST['install']['connect'])
							and array_keys_exist('user', 'password', 'repeat', $_POST['install']['connect'])
						) {
							$con = (object)$_POST['install']['connect'];
							$con->database = $con->user;
						}
						else {
							$con = null;
						}
						if(
							isset($site->user) and is_email($site->user)
							and isset($root->user) and preg_match('/^\w+$/', $root->user)
							and isset($site->password) and preg_match('/' . pattern('password') . '/', $site->password)
							and isset($site->repeat) and $site->repeat === $site->password
							and isset($head->title) and preg_match('/^[\w- ]{5,}$/', $head->title)
							and isset($head->keywords) and preg_match('/^[\w, -]+$/', $head->keywords)
							and isset($head->description) and preg_match('/^[\w-,\.\?\! ]{1,160}$/', $head->description)
							and isset($head->robots) and preg_match('/^(no)?follow, (no)?index$/i', $head->robots)
							and(isset($head->author_g_plus) and is_url($head->author_g_plus))
							and(isset($head->author)) and preg_match('/^[\w- ]{5,}$/', $head->author)
							and(is_null($head->rss) or empty($head->rss) or is_url($head->rss))
							and(is_null($head->publisher) or empty($head->publisher) or is_url($head->publisher))
							and(is_null($head->google_analytics_code) or empty($head->google_analytics_code) or preg_match('/^[A-z]{2}-[A-z\d]{8}-\d$/', $head->google_analytics_code))
							and(is_null($head->author) or empty($head->author) or preg_match('/^[\w- ]{5,}$/', $head->author))
							and (
								is_null($con) or (
									preg_match('/' . pattern('password') . '/', $con->password)
									and $con->password === $con->repeat
									and !file_exists(BASE . '/config/connect.ini')
								)
							)
						) {
							$pdo = new _pdo($root);
							if($pdo->connected) {
								if(is_object($con)) {
									$ini = fopen(BASE . '/config/connect.ini', 'w');
									if($ini) {
										fwrite($ini, 'user = "' . $con->user . '"' . PHP_EOL);
										fwrite($ini, 'password = "' . $con->password . '"' . PHP_EOL);
										fwrite($ini, 'database = "' . $con->user . '"' . PHP_EOL);
										fclose($ini);
										unset($con);
										$created_con = true;
									}
									else {
										$resp->notify(
											'Could not save database connection settings to file',
											'Make sure that config/ is writable'
										)->send();
										exit();
									}
								}
								$con_ini = new ini('connect');
								$database = "`{$pdo->escape($con_ini->database)}`";
								$pdo->query("CREATE DATABASE IF NOT EXISTS {$database}");
								$pdo->prepare("
									GRANT ALL ON {$database}.*
									TO :user@'localhost'
									IDENTIFIED BY :password
								")->bind([
									'user' => $con_ini->user,
									'password' => $con_ini->password
								])->execute();
								unset($DB);
								$DB = new _pdo($con_ini);
								if($DB->connected) {
									if(file_exists(BASE . '/default.sql')) {
										if($DB->restore('default')) {
											$DB->prepare("
												INSERT INTO `head` (`name`, `value`)
												VALUES (:name, :value)
											");
											foreach([
												'title',
												'keywords',
												'description',
												'robots',
												'viewport',
												'charset',
												'author',
												'author_g_plus',
												'publisher',
												'google_analytics_code',
												'rss'
											] as $prop) {
												if(isset($head->$prop)) {
													$DB->bind([
														'name' => $prop,
														'value' => $head->$prop
													])->execute();
												}
											}
											$login = new login($con_ini);
											$login->create_from([
												'user' => $site->user,
												'password' => $site->password
											]);
											$DB->prepare("
												UPDATE `users`
												SET
													`role` = 'admin',
													`g_plus` = :g_plus,
													`name` = :name
												WHERE `user` = :user
											")->bind([
												'user' => $site->user,
												'g_plus' => $head->author_g_plus,
												'name' => $head->author
											])->execute();

											$resp->notify(
												'All done! Congratulations!',
												'Everything is setup and ready to go!'
											)->reload()->send();
										}
										else {
											/**
											 * Unable to restore from default.sql
											 */
											$resp->notify(
												'We have a problem :(',
												'The default database file is invalid. Do a "git pull" and try again. If that still doesn\'t work, file a bug'
											);
										}
									}
									else {
										$resp->notify(
											'We have a problem :(',
											'Database & user setup, but there is no database file to create from'
										);
									}
								}
								else {
									$resp->notify(
										'Something went wrong :(',
										'Sorry, but it looks like you will have to setup the database manually'
									);
								}
							}
							else {
								/**
								 * Unable to connect to default MySQL User
								 */
								$resp->notify(
									'Something went wrong :(',
									'Double check "Root MySQL User credentials"'
								);
							}
						}
						else {
							/**
							 * Form Validation has failed
							 */
							$resp->notify(
								'Something went wrong :(',
								'Please double check your inputs. At least one does not match the correct pattern.'
							);
						}
					}
				}
				/**
				 * Rollback changes if not successful.
				 * Will $resp->send() on success, so
				 * will only reach this point if not successful.
				 */

				if($created_con) {
					unlink(BASE . '/config/connect.ini');
				}
				/*
				//This is potentially EXTREMELY DANGEROUS
				if(isset($pdo) and $pdo->connected) {
					$pdo->prepare("
						DROP USER :user
					")->bind([
						'user' => $con_ini->user
					])->execute();
					$pdo->query("DROP DATABASE {$database}");
				}*/
			} break;
		}

		if(isset($invalid)) {
			$resp->attributes(
				"form[name=\"{$_POST['form']}\"] details",
				'open',
				true
			)->focus(
				$invalid
			)->notify(
				'Double check your inputs',
				'Please correct the selected input'
			);
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
		switch($_REQUEST['datalist']) {
			case 'tags': {
				$datalist = get_datalist('tags');
			} break;

			case 'PHP_errors_files': {
				require_login('admin');
				$datalist = get_datalist('PHP_errors_files');
			} break;
		}
		if(isset($datalist)) {
			$resp->after(
				"[list=\"{$_REQUEST['datalist']}\"]",
				$datalist
			);
		}
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
				require_login('admin');

				$DB->dump();
				$resp->notify(
					'Success',
					"The database has been backed up to {$connect->database}.sql",
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
		$resp->remove(
			'main > :not(aside)'
		)->prepend(
			'body > main',
			ob_get_clean()
		);
	}

	elseif(array_key_exists('hangman', $_REQUEST)) {
		if(preg_match('/^[A-z]$/', $_REQUEST['hangman'])) {
			if(preg_match("/{$_REQUEST['hangman']}/", $session->hangman_phrase)) {
				$session->hangman_matches++;
				preg_match_all("/{$_REQUEST['hangman']}/i", preg_replace('/[^A-z]/', null, $session->hangman_phrase), $matches, PREG_OFFSET_CAPTURE);

				foreach($matches[0] as $index) {
					$pos = (int)$index[1] + 1;
					$resp->text(
						"#hangman_phrase > u:nth-of-type({$pos})",
						$index[0]
					);
				}
				$resp->disable(
					"button[data-request=\"hangman={$_REQUEST['hangman']}\"]"
				);
				if((int)count(array_unique(str_split(preg_replace('/[^A-z]/', null, $session->hangman_phrase)))) === (int)$session->hangman_matches) {
					$resp->notify(
						'Congratulation',
						'You have won'
					)->disable(
						'button[data-request^="hangman"]:not([data-request="hangman=restart"])'
					);
				}
			}
			else {
				$limbs = [
					'head',
					'torso',
					'left_arm',
					'right_arm',
					'left_leg',
					'right_leg'
				];

				if($session->hangman_bad_guesses >= count($limbs) - 1) {
					$resp->notify(
						'You lose',
						'Try again?'
					)->disable(
						'button[data-request^="hangman"]:not([data-request="hangman=restart"])'
					)->text(
						'#hangman_phrase',
						$session->hangman_phrase
					)->attributes(
						'#hangman_' . $limbs[$session->hangman_bad_guesses++],
						'opacity',
						1
					);
				}
				else {
					$resp->attributes(
						'#hangman_' . $limbs[$session->hangman_bad_guesses++],
						'opacity',
						1
					)->disable(
						"button[data-request=\"hangman={$_REQUEST['hangman']}\"]"
					);
				}
			}
		}


		else {
			switch($_REQUEST['hangman']) {
				case 'restart': {
					$phrases = [
						'this is the song that never ends',
						'i love php & javascript',
						'sudo apt-get install life',
						'supercalifragilisticexpialidocious',
						'antidisestablishmentarianism',
						'cards against humanity'
					];

					$session->hangman_phrase = strtoupper($phrases[mt_rand(0, count($phrases) - 1)]);
					$session->hangman_matches = 0;
					$session->hangman_bad_guesses = 0;

					$resp->remove(
						'#hangman_phrase'
					)->after(
						'section svg',
						'<h1 id="hangman_phrase">' . preg_replace('/[A-z]/', '<u>&nbsp;&nbsp;</u>', $session->hangman_phrase) . '</h1>'
					)->enable(
						'button[data-request^="hangman"]'
					)->attributes(
						'svg path[id^=hangman]',
						'opacity',
						0
					);
				} break;
			}
		}
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
