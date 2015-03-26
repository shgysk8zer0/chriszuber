<?php
$resp = \shgysk8zer0\Core\JSON_Response::load();

switch($_POST['request']) {
	case 'nonce':
		$resp->sessionStorage(
			'nonce',
			$session->nonce
		);
	 break;
}
exit($resp);
