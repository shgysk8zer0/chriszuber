<?php
	$resp = \core\json_response::load();
	check_nonce();
	switch(trim($_POST['form'])) {
		case 'login': {
			$invalid = check_inputs([
				'user' => is_email($_POST['user']),
				'password' => pattern('password')
			]);

			if(is_null($invalid)) {
				$login->login_with([
					'user' => $_POST['user'],
					'password' => $_POST['password']
				]);

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
					)->notify(
						'Welcome back,',
						$login->user
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

		case 'compose_email': {
			require_login('admin');
			$email = new \core\email(
				array_map('trim', explode(',', $_POST['compose_email']['to'])),
				trim($_POST['compose_email']['subject']),
				$_POST['compose_email']['message']
			);

			if($email->send(true)) {
				$resp->notify(
					'Success!',
					'Email Sent',
					'images/icons/envelope.png'
				)->remove(
					'#email_dialog'
				);
			}
			else {
				$resp->notify(
					'Failed!',
					'Unable to send email, check your Internet connection',
					'images/icons/envelope.png'
				);
			}
		} break;

		case 'email_admin': {
			if(is_email($_POST['email_admin']['from'])) {
				$email = new \core\email(
					$_SERVER['SERVER_ADMIN'],
					$_POST['email_admin']['subject'],
					strip_tags($_POST['email_admin']['message'])
				);
				$email->reply_to = $_POST['email_admin']['from'];
				if($email->send()) {
					$resp->notify(
						'Thanks!',
						"Email sent.\nI will try to get back to you as soon as possible",
						'images/icons/envelope.png'
					)->clear(
						'email_admin'
					)->close(
						'#email_admin_dialog'
					);
				}
			}
			else {
				$invalid = 'form[name="email_admin"] input[name="email_admin[from]"]';
			}
		} break;

		case 'tag_search': {
			$invalid = check_inputs([
				'tags' => '[\w- ]+'
			], $_REQUEST);

			if(is_null($invalid)) {
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

					$template = \core\template::load('tags');

					foreach($posts as $post) {
						$datetime = new \core\simple_date($post->created);
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

			$title = urldecode(trim(strip_tags($_POST['title'])));
			$description = trim($_POST['description']);
			$keywords = urldecode(trim(strip_tags($_POST['keywords'])));
			$content = trim($_POST['content']);

			$invalid = check_inputs([
				'title' => true,
				'description' => '.{10,160}',
				'keywords' => '[\w-\, ]+',
				'content' => true
			], [
				'title' => $title,
				'description' => $description,
				'keywords' => $keywords,
				'content' => $content
			]);

			if(is_null($invalid)) {

				$user = $DB->prepare('
					SELECT `g_plus`, `name`
					FROM `users`
					WHERE `user` = :user
					LIMIT 1
				')->bind([
					'user' => $login->user
				])->execute()->get_results(0);

				/*$title = urldecode(trim(strip_tags($_POST['title'])));
				$description = trim($_POST['description']);
				$keywords = urldecode(trim(strip_tags($_POST['keywords'])));
				$content = trim($_POST['content']);*/
				$author = $user->name;
				$url = urlencode(strtolower(preg_replace('/\W+/', ' ', $title)));
				$time = new \core\simple_date();

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
					$template = \core\template::load('posts');
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

			$post = [
				'title' => urldecode(trim(strip_tags($_POST['title']))),
				'keywords' => urldecode(trim(strip_tags(str_replace('</a>', '</a>,', $_POST['keywords'])), ',')),
				'description' => trim($_POST['description']),
				'content' => trim($_POST['content']),
				'old_title' => urldecode(trim($_POST['old_title']))
			];

			$invalid = check_inputs([
				'title' => true,
				'old_title' => true,
				'description' => '.{10,160}',
				'keywords' => '[\w-, ]+',
				'content' => true
			], $post);

			if(is_null($invalid)) {
				$DB->prepare("
					UPDATE `posts`
					SET `title` = :title,
					`keywords` = :keywords,
					`description` = :description,
					`content` = :content
					WHERE `title` = :old_title
					LIMIT 1
				")->bind($post);
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
			$invalid = check_inputs([
				'comment_author' => '[\w\.\-, ]+',
				'comment_email' => is_email($_POST['comment_email'])
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
				$template = \core\template::load('comments');
				$author = $_POST['comment_author'];
				$author_url = (array_key_exists('comment_url', $_POST) and is_url($_POST['comment_url'])) ? $_POST['comment_url'] : '';
				$author_email = $_POST['comment_email'];
				$time = date('Y-m-d H:i:s');
				$post_title = ucwords(urldecode($post));
				$email = new \core\email(
					$_SERVER['SERVER_ADMIN'],
					"New comment on {$post_title} by {$author}",
					\core\template::load(
						'comment_created_notification'
					)->author(
						$author
					)->author_url(
						$author_url
					)->author_email(
						"{$author} <{$author_email}>"
					)->time(
						date('r', strtotime($time))
					)->comment(
						$comment
					)->post(
						ucwords(urldecode($post))
					)->post_url(
						URL . "/posts/{$post}"
					)->out(),
					[
						'Reply-To' => "{$author} <{$author_email}>"
					]
				);

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
				$email->send(true);
			}
		} break;

		case 'install': {
			/**
			 * Sets up the database, creates user, etc
			 *
			 * @todo use check_inputs everywhere!
			 */

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
						$pdo = new PDO($root);
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
							$con_ini = \core\resources\Parser::parse('connect.json');
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
							$DB = new PDO($con_ini);
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
										$login = new \core\login($con_ini);
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
							)->focus(
								'form[name="install"] input[name="[root][user]"]'
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
?>
