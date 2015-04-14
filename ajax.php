<?php
if (empty($_REQUEST)) {
	load('handlers/pages');
} elseif (array_key_exists('load_form', $_POST)) {
	load('handlers/load_form');
} elseif (array_key_exists('form', $_POST)) {
	load('handlers/form');
} elseif (array_key_exists('load_menu', $_POST)) {
	load('handlers/load_menu');
} elseif (array_key_exists('datalist', $_REQUEST)) {
	load('handlers/datalist');
} elseif (array_key_exists('template', $_REQUEST)) {
	load('handlers/template');
} elseif (array_key_exists('action', $_POST)) {
	load('handlers/action');
} elseif (array_key_exists('request', $_POST)) {
	load('handlers/request');
} elseif (array_key_exists('debug', $_POST)) {
	load('handlers/debug');
} elseif (array_key_exists('hangman', $_REQUEST)) {
	load('handlers/hangman');
}

exit(\shgysk8zer0\Core\JSON_Response::load());
