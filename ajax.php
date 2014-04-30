<?php
if(isset($_POST['load'])){
	switch($_POST['load']) {
		default:
			json_response([
				'html' => [
					'main' => load_results($_POST['load'])
				]
			]);
	}
}
	exit();
?>