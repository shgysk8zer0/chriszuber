<?php
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
