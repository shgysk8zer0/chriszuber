<?php
$resp = \shgysk8zer0\Core\JSON_Response::load();
require_login('admin');

switch($_POST['debug']) {
	case 'headers':
		$console->info(['HTTP Headers' => headers_list()]);
		break;

	case 'extensions':
		$console->info(['PHP extensions' => get_loaded_extensions()]);
		break;

	case 'modules':
		$console->info(['Apache Modules' => apache_get_modules()]);
		break;

	case '_SERVER':
		$console->info(['$_SERVER' => $_SERVER]);
		break;

	case '_SESSION':
		$console->info(['$_SESSION' => $_SESSION]);
		break;

	case '_COOKIES':
		$console->info(['$_COOKIES' => $_COOKIE]);
		break;

	case 'vars':
		$console->info(['Defined Vars' => get_defined_vars()]);
		break;

	default:
		$console->error(sprintf('Unhandled debug request: %s', $_POST['debug']));
}
$resp->notify(
	'Debug info sent to console',
	'Check your developer console'
);
exit($resp);
