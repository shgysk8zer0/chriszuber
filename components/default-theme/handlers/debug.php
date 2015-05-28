<?php
$resp = \shgysk8zer0\Core\JSON_Response::load();
require_login('admin');

switch($_POST['debug']) {
	case 'headers':
		$resp->dir(['HTTP Headers' => headers_list()]);
		break;

	case 'extensions':
		$resp->dir(['PHP extensions' => get_loaded_extensions()]);
		break;

	case 'modules':
		$resp->dir(['Apache Modules' => apache_get_modules()]);
		break;

	case '_SERVER':
		$resp->dir(['$_SERVER' => $_SERVER]);
		break;

	case '_SESSION':
		$resp->dir(['$_SESSION' => $_SESSION]);
		break;

	case '_COOKIES':
		$resp->dir(['$_COOKIES' => $_COOKIE]);
		break;

	case 'vars':
		$resp->dir(['Defined Vars' => get_defined_vars()]);
		break;

	default:
		$resp->error(sprintf('Unhandled debug request: %s', $_POST['debug']));
}
$resp->notify(
	'Debug info sent to console',
	'Check your developer console'
);
exit($resp);
