<?php
	$session = session::load();
	$login = login::load();

	if(array_key_exists('load', $_POST)){
		switch($_POST['load']) {
			default:
				json_response([
					'html' => [
						'main' => load_results($_POST['load'])
					]
				]);
		}
	}

	elseif(array_key_exists('load_form', $_POST)) {
		switch($_POST['load_form']) {
			case 'login':
				json_response([
					'html' => [
						'main' => load_results('forms/login')
					]
				]);
				break;
			case 'new_post':
				($login->logged_in) ? json_response([
					'html' => [
						'main' => load_results('forms/new_post')
					]
				]) : json_response([
					'notify' => [
						'title' => 'We have a problem',
						'body' => 'You must be logged in to do that'
					],
					'html' => [
						'main' => load_results('forms/login')
					]
				]);
		}
	}

	elseif(array_key_exists('form', $_POST)) {
		switch($_POST['form']) {
			case 'login':
				if(array_keys_exist('user', 'password', 'nonce', $_POST) and $_POST['nonce'] === $session->nonce) {
					$login->login_with($_POST);
					if($login->logged_in) {
						$session->setUser($login->user)->setPassword($login->password)->setRole($login->role)->setLogged_In(true);
						json_response([
							'attributes' => [
								'menu[label=Account] menuitem:not([label=Logout])' => [
									'disabled' => true
								],
								'menuitem[label=Logout]' => [
									'disabled' => false
								],
								'body > main' => [
									'contextmenu' => false,
									'data-menu' => 'admin'
								],
								'remove' => 'main > *'
							]
						]);
					}
					else {
						json_response([
							'notify' => [
								'title' => 'Login not accepted',
								'body' => 'Check your email & password',
								'icon' => 'images/icons/people.png'
							]
						]);
					}
				}
				else {
					json_response([
						'notify' => [
							'title' => 'Login not accepted',
							'body' => 'Check your email & password',
							'icon' => 'images/icons/people.png'
						]
					]);
				}
				break;
		}
	}

	elseif(array_key_exists('load_menu', $_POST)) {
		switch($_POST['load_menu']) {
			default:
				json_response([
					'prepend' => [
						'body' => load_results("menus/{$_POST['load_menu']}")
					]
				]);
		}
	}

	elseif(array_key_exists('action', $_POST)) {
		switch($_POST['action']) {
			case 'logout':
				$login->logout();
				json_response([
					'attributes' => [
						'menu[label=Account] menuitem[label=Login]' => [
							'disabled' => false
						],
						'menu[label=Account] menuitem[label=Logout]' => [
							'disabled' => true
						],
						'body > main' => [
							'contextmenu' => false
						]
					],
					'remove' => 'main > *'
				]);
				break;
		}
	}

	else http_status(404);
	exit();
?>
