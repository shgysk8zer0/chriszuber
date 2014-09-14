<?php
	/**
	 * Interface for common magic methods
	 *
	 * @author Chris Zuber <shgysk8zer0@gmail.com>
	 * @package core_shared
	 * @version 2014-09-09
	 * @copyright 2014, Chris Zuber
	 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3 (GPL-3.0)
	 * This program is free software; you can redistribute it and/or
	 * modify it under the terms of the GNU General Public License
	 * as published by the Free Software Foundation, either version 3
	 * of the License, or (at your option) any later version.
	 *
	 * This program is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	 * GNU General Public License for more details.
	 *
	 * You should have received a copy of the GNU General Public License
	 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
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
