<?php
	/**
	 * Interface for common magic methods
	 *
	 * @author Chris Zuber <shgysk8zer0@gmail.com>
	 * @copyright 2014, Chris Zuber
	 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3 (GPL-3.0)
	 * @package core_shared
	 * @version 2014-09-09
	 *
	*/

	namespace core;
	interface magic_methods {
		/**
		 * Setter method
		 *
		 * @param string $key
		 * @param mixed $value
		 * @example $someClass->$key = $value;
		 * @return void
		*/

		public function __set($key, $value);

		/**
		 * Getter method
		 *
		 * @param string $key
		 * @return mixed
		 * @example $var = $someClass->$key;
		*/

		public function __get($key);

		/**
		 * Magic isset method
		 *
		 * @param string $key
		 * @return boolean
		 * @example isset($someClass->$key);
		*/

		public function __isset($key);

		/**
		 * Magic unset method
		 *
		 * @param string $key
		 * @return void
		 * @example unset($someClass->$key);
		*/

		public function __unset($key);

		/**
		 * Magic call method
		 * Actual functionality to be defined in the class
		 *
		 * @param string $name
		 * @param array $arguments
		 * @return mixed
		 * @example $someClass->$name($arguments[0][, $arguments[1]...]);
		*/

		public function __call($name, array $arguments);
	}
?>
