<?php
	require_once __DIR__ . DIRECTORY_SEPARATOR . 'functions.php';
	init();
	$reflect = new \shgysk8zer0\Core\Tests\Resources\Test();
	exit($reflect->getFailedAsserts());
