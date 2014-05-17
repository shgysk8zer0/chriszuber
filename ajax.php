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
		}
	}

	elseif(array_key_exists('form', $_POST)) {
		switch($_POST['form']) {
			case 'login':
				if(array_keys_exist('user', 'password', 'nonce', $_POST) and $_POST['nonce'] = $session->nonce) {
					$login->login_with($_POST);
					if($login->logged_in) {
						$session->setUser($login->user)->setPassword($login->password)->setRole($login->role);
						json_response([
							'notify' => [
								'title' => 'Login: ',
								'body' => 'Approved'
							],
							'attributes' => [
								'menu[label=Account] menuitem[label=Login' => [
									'disabled' => true
								],
								'menu[label=Account] menuitem[label=Logout' => [
									'disabled' => false
								]
							]
						]);
					}
					else {
						json_response([
							'notify' => [
								'title' => 'Login: ',
								'body' => 'Rejected'
							]
						]);
					}
				}
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

	else http_status(404);
	exit();
?>
