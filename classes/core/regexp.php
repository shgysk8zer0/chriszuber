<?php
	/**
	 * Makes easy use of simple regular expressions
	 *
	 * @author Chris Zuber <shgysk8zer0@gmail.com>
	 * @package core_shared
	 * @version 2014-04-19
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
	 *
	 * @example:
	 * 		$reg = new regexp([$string]);
	 * 		$some_var = $reg->replace('foo')->with('bar')[->in($string)]->execute()
	 */

	namespace core;
	class regexp{

		protected $pattern, $replacement, $limit = -1, $find;
		public $in, $result;

		/**
		 * Gets database connection info from /connect.ini (stored in $site)
		 * Uses that data to create a new PHP Data Object
		 *
		 * @param [string $str]
		 * @return void
		 * @example $reg = new regexp([$string])
		 */

		public function __construct($str = null) {
			$this->pattern = array();
			$this->replacement = array();
			if(is_string($str)) $this->in = $str;
		}

		/**
		 * @param string $key
		 * @return boolean
		 * @example "isset({$reg->key})"
		 */

		public function __isset($name) {
			return isset($this->$name);
		}

		/**
		 * Set pattern according to presets
		 *
		 * @param string $type
		 * @return self
		 * @uses functions.php::pattern()
		 * @example "$reg->set_pattern('number')" sets $this->patttern to "\d+(\.\d{1,})?"
		 */

		public function set_pattern($type = null) {
			$this->pattern = pattern($type);
			return $this;
		}

		/**
		 * Adds a new pattern to $pattern[]
		 *
		 * @param string $str
		 * @return self
		 */

		public function replace($str = null) {
			array_push($this->pattern, $this->regexp($str));
			return $this;
		}

		/**
		 * Adds a new replacement to $replacement
		 *
		 * @param strign $str
		 * @return self
		 */

		public function with($str = null) {
			array_push($this->replacement, (string)$str);
			return $this;
		}

		/**
		 * RegExp at end of string
		 *
		 * @param string $str
		 * @return boolean
		 */

		public function ends_with($str = null) {
			$this->find = $this->regexp($str, 'end');
			return $this->test();
		}

		/**
		 * RegExp at beginning of string
		 *
		 * @param string $str
		 * @return boolean
		 */

		public function begins_with($str) {
			$this->find = $this->regexp((string)$str, 'begin');
			return $this->test();
		}

		/**
		 * RegExp of the full string. Begin and end
		 *
		 * @param string $str
		 * @return boolean
		 */

		public function is($str = null) {
			$this->find = $this->regexp($str, 'full');
			return $this->test();
		}

		/**
		 * Location agnostic RegExp
		 *
		 * @param string $str
		 * @return boolean
		 */

		public function has($str = null) {
			$this->find = $this->regexp($str, null);
			return $this->test();
		}

		/**
		 * Creates the RegExp format '/[^]pattern[$]/', replacing dangerous characters
		 *
		 * @param string $str[, string $loc]
		 * @return string (regular expression)
		 */

		public function regexp($str = null, $loc = null) {
			$pattern = preg_quote((string)$str, '/');
			switch($loc) {
				case 'begin':
				case '^':
					$pattern = "/^$pattern/";
					break;
				case 'end':
				case '$':
					$pattern = "/$pattern$/";
					break;
				case 'full':
				case '=':
					$pattern = "/^$pattern$/";
					break;
				default:
					$pattern = "/$pattern/";
			}
			return $pattern;
		}

		/**
		 * Returns boolean result of a RegExp search
		 *
		 * @param void
		 * @return boolean
		 */

		public function test() {
			return preg_match($this->find, $this->in);
		}

		/**
		 * In the case of finding a needle in a haystack, this sis the needle
		 *
		 * @param string $str[, string $loc]
		 * @return self
		 */

		public function find($str = null, $loc = null) {
			$this->find = $this->regexp($str, $full);
			return $this;
		}

		/**
		 * In the case of finding a needle in a haystack, this sis the needle
		 *
		 * @param string $str
		 * @return self
		 */

		public function in($str) {
			$this->in = (string)$str;
			return $this;
		}

		/**
		 * Optional limit to replacements. Defaults to unlimited
		 *
		 * @param int $n
		 * @return self
		 */

		public function limit($n = 0) {
			$this->limit = (int)$n;
			return $this;
		}

		/**
		 * Runs the RegExp replacement, modifies and returns the string
		 *
		 * @param void
		 * @return string
		 */

		public function execute($update = true) {
			if($update) {
				$this->in = preg_replace($this->pattern, $this->replacement, $this->in, $this->limit);
				return $this->in;
			}
			else return preg_replace($this->pattern, $this->replacement, $this->in, $this->limit);
		}

		/**
		 * @depreciated ?
		 * @param void
		 * @return string
		 */

		public function value() {
			return $this->in;
		}

		/**
		 * Returns input patterns for html inputs
		 *
		 * @param string $type
		 * @return boolean
		 */

		public function matches_pattern($type = null) {
			$pattern = pattern($type);
			$this->find = "/^$pattern$/";
			return $this->test();
		}

		public function debug() {
			debug($this);
		}
	}
?>
