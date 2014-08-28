<?php
	$resp = json_response::load();
	switch($_POST['request']) {
		case 'nonce': {
			$resp->sessionStorage(
				'nonce',
				$session->nonce
			);
		}
	}
?>
