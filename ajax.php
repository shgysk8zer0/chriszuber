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
	exit();
?>