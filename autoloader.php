<?php
set_include_path(
	__DIR__ . DIRECTORY_SEPARATOR . getenv('AUTOLOAD_DIR')
	. PATH_SEPARATOR . __DIR__ . DIRECTORY_SEPARATOR . getenv('CONFIG_DIR')
	. PATH_SEPARATOR . get_include_path()
);
spl_autoload_extensions(getenv('AUTOLOAD_EXTS'));
spl_autoload_register(getenv('AUTOLOAD_FUNC'));
