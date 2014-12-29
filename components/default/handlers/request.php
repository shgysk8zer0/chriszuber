<?php
	$resp = \shgysk8zer0\core\json_response::load();
	switch($_POST['request']) {
		case 'nonce': {
			$resp->sessionStorage(
				'nonce',
				$session->nonce
			);
		}
	}
?>
